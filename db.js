import './componentes/sqlite3-worker1-promiser-bundler-friendly.js';

const IPFS_API_URL = 'https://ipfs.infura.io:5001/api/v0';
const IPFS_CID_STORAGE_KEY = 'sqlite-ipfs-cid';

async function ipfsAdd(bytes) {
    const form = new FormData();
    form.append('file', new Blob([bytes]), 'academica.sqlite3');
    const res = await fetch(`${IPFS_API_URL}/add`, { method: 'POST', body: form });
    if (!res.ok) throw new Error(`IPFS add failed (${res.status})`);
    const text = await res.text();
    const json = text.split('\n').filter(Boolean).map((l) => JSON.parse(l)).pop();
    return json?.Hash || json?.Cid || null;
}

async function ipfsCat(cid) {
    const res = await fetch(`${IPFS_API_URL}/cat?arg=${encodeURIComponent(cid)}`);
    if (!res.ok) throw new Error(`IPFS cat failed (${res.status})`);
    return new Uint8Array(await res.arrayBuffer());
}

function getSavedIpfsCid() {
    try {
        return localStorage.getItem(IPFS_CID_STORAGE_KEY);
    } catch {
        return null;
    }
}

function setSavedIpfsCid(cid) {
    try {
        localStorage.setItem(IPFS_CID_STORAGE_KEY, cid);
    } catch {
        // ignore
    }
}

class SQLiteORM {
    constructor() {
        this.sqlite3 = null;
        this.alumnos = new Table(this, 'alumnos', 'idAlumno');
        this.materias = new Table(this, 'materias', 'idMateria');
        this.docentes = new Table(this, 'docentes', 'idDocente');
        this.matriculas = new Table(this, 'matriculas', 'idMatricula');
        this.inscripciones = new Table(this, 'inscripciones', 'idInscripcion');
        this.isReady = false;
        this.localDisabled = false;
        this._readyPromise = this.init();
        this._ipfsSaveTimer = null;
        this._ipfsSaveInProgress = null;
    }

    async init() {
        try {
            // Pre-check para OPFS (para que el error sea claro y no termine como "[object Object]").
            const opfsPreflight = {
                isSecureContext: typeof window !== 'undefined' ? window.isSecureContext : null,
                crossOriginIsolated:
                    typeof window !== 'undefined' ? !!window.crossOriginIsolated : null,
                hasGetDirectory: !!(navigator?.storage && typeof navigator.storage.getDirectory === 'function'),
                hasSharedArrayBuffer: typeof SharedArrayBuffer !== 'undefined',
                hasAtomics: typeof Atomics !== 'undefined'
            };

            // Detectar falta de encabezados COOP/COEP (suele ser el motivo más común de que OPFS no funcione).
            try {
                const res = await fetch(window.location.href, { method: 'GET' });
                const coop = res.headers.get('Cross-Origin-Opener-Policy');
                const coep = res.headers.get('Cross-Origin-Embedder-Policy');
                if (!coop || !coep) {
                    console.warn('La respuesta del servidor no incluye los encabezados COOP/COEP necesarios para OPFS.', {coop, coep});
                }
            } catch (e) {
                console.warn('No se pudo verificar encabezados COOP/COEP (no crítico):', e);
            }

            console.log('OPFS preflight:', opfsPreflight);

            // Inicializar Promiser que corre en un Web Worker.
            // Algunos errores de OPFS pueden ser transitorios, por lo que reintentamos.
            const createPromiser = () => new Promise((resolve, reject) => {
                const _promiser = self.sqlite3Worker1Promiser({
                    onready: () => resolve(_promiser),
                    worker: () => new Worker(new URL('./componentes/sqlite3-worker1-bundler-friendly.mjs', import.meta.url), { type: 'module' }),
                    onerror: (err) => reject(err)
                });
            });

            let lastInitError;
            for (let attempt = 1; attempt <= 3; attempt++) {
                try {
                    this.sqlite3 = await createPromiser();
                    break;
                } catch (e) {
                    lastInitError = e;
                    console.warn(`Error inicializando worker SQLite (intento ${attempt}/3):`, e);
                    await new Promise((r) => setTimeout(r, 200 * attempt));
                }
            }
            if (!this.sqlite3) {
                throw lastInitError || new Error('No se pudo inicializar el worker SQLite.');
            }

            // Pequeña pausa para asegurar que el worker tenga tiempo de estabilizarse antes de operar.
            await new Promise(resolve => setTimeout(resolve, 50));

            // Consultar la configuración del worker para saber qué VFS están disponibles.
            const conf = await this.sqlite3('config-get', {});
            const vfsList = conf?.result?.vfsList || [];
            console.log('VFS disponibles en el Worker:', vfsList);

            const preferredVfs = ['opfs-sahpool', 'opfs', 'wasmfs', 'memdb', 'kvvfs'];
            const candidates = preferredVfs.filter(v => vfsList.includes(v));
            if (!candidates.length) {
                throw new Error('No hay VFS soportado disponible en este navegador/entorno: ' + JSON.stringify(vfsList));
            }

            const isOpfsVfs = (v) => (v === 'opfs' || v === 'opfs-sahpool');

            const savedCid = getSavedIpfsCid();
            let ipfsBytes = null;
            if (savedCid) {
                try {
                    console.log('Cargando DB desde IPFS CID:', savedCid);
                    ipfsBytes = await ipfsCat(savedCid);
                    console.log('DB cargada desde IPFS, tamaño', ipfsBytes.length);
                } catch (e) {
                    console.warn('No se pudo cargar DB desde IPFS (continuando sin ella):', e);
                }
            }

            const ensureOpfsPreflight = () => {
                if (!opfsPreflight.isSecureContext) {
                    throw new Error('OPFS requiere un contexto seguro (HTTPS o localhost).');
                }
                if (!opfsPreflight.crossOriginIsolated) {
                    throw new Error(
                        'OPFS (async) requiere cross-origin isolation. Configura COOP/COEP en el servidor ' +
                        '(por ejemplo: COOP=same-origin y COEP=credentialless o require-corp).'
                    );
                }
                if (!opfsPreflight.hasGetDirectory) {
                    throw new Error('OPFS requiere navigator.storage.getDirectory() en este navegador/entorno.');
                }
                if (!opfsPreflight.hasSharedArrayBuffer || !opfsPreflight.hasAtomics) {
                    throw new Error('OPFS requiere SharedArrayBuffer y Atomics habilitados (cross-origin isolation).');
                }
            };

            const openWithVfs = async (vfs) => {
                if (isOpfsVfs(vfs)) {
                    ensureOpfsPreflight();
                }

                const filename = (vfs === 'kvvfs')
                    ? ':localStorage:'
                    : `file:academica.sqlite3?vfs=${vfs}`;

                console.log('Abriendo base de datos usando VFS:', vfs, 'filename:', filename);
                const openArgs = { filename };
                if (ipfsBytes) openArgs.byteArray = ipfsBytes;

                const openRes = await this.sqlite3('open', openArgs);
                console.log(`Resultado de open (${vfs}):`, openRes);
                return openRes;
            };

            let openResult;
            let lastOpenError = null;
            let chosenVfs = null;
            for (const vfs of candidates) {
                try {
                    openResult = await openWithVfs(vfs);
                    chosenVfs = vfs;
                    console.log('Base de datos abierta con VFS:', vfs);
                    break;
                } catch (e) {
                    console.warn('No se pudo abrir DB con VFS', vfs, e);
                    lastOpenError = e;
                }
            }
            if (!openResult) {
                throw lastOpenError || new Error('No se pudo abrir la base de datos con ninguno de los VFS disponibles.');
            }
            if (!isOpfsVfs(chosenVfs)) {
                console.warn('Se abrió la DB usando un VFS distinto a OPFS. OPFS no funcionó correctamente en este entorno.');
            }

            // Crear las tablas (si no existen) y avanzar el esquema en caso de versiones anteriores
            await this.sqlite3('exec', {
                sql: `
                CREATE TABLE IF NOT EXISTS alumnos (
                    id INTEGER,
                    idAlumno TEXT PRIMARY KEY,
                    codigo TEXT,
                    nombre TEXT,
                    direccion TEXT,
                    email TEXT,
                    telefono TEXT,
                    hash TEXT
                );
                CREATE TABLE IF NOT EXISTS materias (
                    id INTEGER,
                    idMateria TEXT PRIMARY KEY,
                    codigo TEXT,
                    nombre TEXT,
                    uv TEXT
                );
                CREATE TABLE IF NOT EXISTS docentes (
                    id INTEGER,
                    idDocente TEXT PRIMARY KEY,
                    codigo TEXT,
                    nombre TEXT,
                    direccion TEXT,
                    email TEXT,
                    telefono TEXT,
                    escalafon TEXT
                );
                CREATE TABLE IF NOT EXISTS matriculas (
                    id INTEGER,
                    idMatricula TEXT PRIMARY KEY,
                    codigo TEXT,
                    fecha TEXT,
                    idAlumno TEXT
                );
                CREATE TABLE IF NOT EXISTS inscripciones (
                    id INTEGER,
                    idInscripcion TEXT PRIMARY KEY,
                    idMatricula TEXT,
                    idAlumno TEXT,
                    idMateria TEXT,
                    ciclo TEXT,
                    fecha TEXT
                );
            `});

            // Guardar en IPFS después de inicializar el esquema (solo si se abrió correctamente)
            this.scheduleSaveToIpfs();

            // Si el esquema existente no contenía ciertos campos (por ejemplo en versiones antiguas), agregar columnas necesarias.
            await this.ensureColumnExists('inscripciones', 'idAlumno', 'TEXT');
            await this.ensureColumnExists('inscripciones', 'ciclo', 'TEXT');
            await this.ensureColumnExists('inscripciones', 'fecha', 'TEXT');

            console.log('Esquema OPFS cargado correctamente en Web Worker Asincrónico.');
            this.isReady = true;
        } catch (err) {
            const errInfo = err instanceof Error
                ? { name: err.name, message: err.message, stack: err.stack }
                : err;
            console.error('Error inicializando SQLite OPFS Promiser:', errInfo);
            console.warn('La base de datos local OPFS no está disponible. La aplicación seguirá funcionando sin almacenamiento local.');

            // Deshabilitar el uso local para evitar errores repetidos en el resto de la aplicación.
            this.localDisabled = true;
            this.sqlite3 = null;
            this.isReady = true;
            return;
        }
    }

    async saveToIpfs() {
        if (this.localDisabled || !this.sqlite3) return;
        if (this._ipfsSaveInProgress) return this._ipfsSaveInProgress;

        this._ipfsSaveInProgress = (async () => {
            try {
                const res = await this.sqlite3('export');
                const bytes = res?.byteArray;
                if (!bytes) return null;
                const cid = await ipfsAdd(bytes);
                if (cid) {
                    setSavedIpfsCid(cid);
                    console.log('DB exportada a IPFS, CID:', cid);
                }
                return cid;
            } catch (e) {
                console.warn('No se pudo guardar la DB en IPFS:', e);
                return null;
            } finally {
                this._ipfsSaveInProgress = null;
            }
        })();

        return this._ipfsSaveInProgress;
    }

    scheduleSaveToIpfs() {
        if (this.localDisabled || !this.sqlite3) return;
        if (this._ipfsSaveTimer) clearTimeout(this._ipfsSaveTimer);
        this._ipfsSaveTimer = setTimeout(() => {
            this.saveToIpfs().catch(() => {});
        }, 600);
    }

    async ensureColumnExists(tableName, columnName, type) {
        // Verificar si la columna ya existe antes de intentar agregarla.
        const columns = [];
        await this.sqlite3('exec', {
            sql: `PRAGMA table_info(${tableName})`,
            rowMode: 'object',
            callback: (res) => {
                if (res.row) columns.push(res.row.name);
            }
        });
        if (!columns.includes(columnName)) {
            console.log(`Agregando columna faltante '${columnName}' a la tabla '${tableName}'`);
            await this.sqlite3('exec', {
                sql: `ALTER TABLE ${tableName} ADD COLUMN ${columnName} ${type}`
            });
        }
    }

    async waitForReady() {
        if (!this.isReady) {
            await this._readyPromise;
        }
        if (!this.isReady && !this.localDisabled) {
            throw new Error('SQLiteORM no está listo: falló la inicialización (OPFS).');
        }
        // Si la inicialización local falló, seguimos (solo backend).
    }
}

class Table {
    constructor(dbObj, tableName, pk) {
        this.dbObj = dbObj;
        this.tableName = tableName;
        this.pk = pk;
    }

    async getTableInfo() {
        if (this.dbObj.localDisabled) {
            // No tenemos base local, evitamos ejecutar SQL.
            this.columns = new Set();
            return;
        }

        if (!this.columns) {
            const tableInfo = [];
            await this.dbObj.sqlite3('exec', {
                sql: `PRAGMA table_info(${this.tableName})`,
                rowMode: 'object',
                callback: (res) => {
                    if (res.row) tableInfo.push(res.row.name);
                }
            });
            this.columns = new Set(tableInfo);
        }
    }

    async put(item) {
        await this.dbObj.waitForReady();
        if (this.dbObj.localDisabled) return;

        await this.getTableInfo();

        const validItem = {};
        for (const [k, v] of Object.entries(item)) {
            if (this.columns.has(k)) {
                if (v !== null && typeof v === 'object' && !(v instanceof ArrayBuffer) && !(v instanceof Uint8Array)) {
                    validItem[k] = v.toString() !== '[object Object]' ? v.toString() : JSON.stringify(v);
                } else {
                    validItem[k] = v;
                }
            }
        }

        const keys = Object.keys(validItem);
        const values = Object.values(validItem);
        const placeholders = keys.map(() => '?').join(', ');
        const sql = `INSERT OR REPLACE INTO ${this.tableName} (${keys.join(', ')}) VALUES (${placeholders})`;
        await this.dbObj.sqlite3('exec', {
            sql: sql,
            bind: values
        });
        this.dbObj.scheduleSaveToIpfs();
    }

    async delete(id) {
        await this.dbObj.waitForReady();
        if (this.dbObj.localDisabled) return;
        await this.dbObj.sqlite3('exec', {
            sql: `DELETE FROM ${this.tableName} WHERE ${this.pk} = ?`,
            bind: [id]
        });
        this.dbObj.scheduleSaveToIpfs();
    }

    async bulkAdd(items) {
        await this.dbObj.waitForReady();
        if (this.dbObj.localDisabled) return;
        if (!items || !items.length) return;
        await this.getTableInfo();

        await this.dbObj.sqlite3('exec', { sql: 'BEGIN TRANSACTION' });
        try {
            for (let item of items) {
                const validItem = {};
                for (const [k, v] of Object.entries(item)) {
                    if (this.columns.has(k)) {
                        if (v !== null && typeof v === 'object' && !(v instanceof ArrayBuffer) && !(v instanceof Uint8Array)) {
                            validItem[k] = v.toString() !== '[object Object]' ? v.toString() : JSON.stringify(v);
                        } else {
                            validItem[k] = v;
                        }
                    }
                }
                const keys = Object.keys(validItem);
                const values = Object.values(validItem);
                const placeholders = keys.map(() => '?').join(', ');
                const sql = `INSERT OR REPLACE INTO ${this.tableName} (${keys.join(', ')}) VALUES (${placeholders})`;
                await this.dbObj.sqlite3('exec', { sql, bind: values });
            }
            await this.dbObj.sqlite3('exec', { sql: 'COMMIT' });
            this.dbObj.scheduleSaveToIpfs();
        } catch (e) {
            await this.dbObj.sqlite3('exec', { sql: 'ROLLBACK' });
            console.error('Rollback en bulkAdd', e);
        }
    }

    filter(predicate) {
        return {
            toArray: async () => {
                await this.dbObj.waitForReady();
                if (this.dbObj.localDisabled) return [];
                const rows = [];
                await this.dbObj.sqlite3('exec', {
                    sql: `SELECT * FROM ${this.tableName}`,
                    rowMode: 'object',
                    callback: (res) => {
                        if (res.row && predicate(res.row)) {
                            rows.push(res.row);
                        }
                    }
                });
                return rows;
            }
        };
    }

    orderBy(field) {
        return {
            filter: (predicate) => {
                return {
                    toArray: async () => {
                        await this.dbObj.waitForReady();
                        if (this.dbObj.localDisabled) return [];
                        const rows = [];
                        await this.dbObj.sqlite3('exec', {
                            sql: `SELECT * FROM ${this.tableName} ORDER BY ${field} ASC`,
                            rowMode: 'object',
                            callback: (res) => {
                                if (res.row && predicate(res.row)) {
                                    rows.push(res.row);
                                }
                            }
                        });
                        return rows;
                    }
                };
            }
        };
    }
}

const db = new SQLiteORM();
window.db = db;
export default db;
