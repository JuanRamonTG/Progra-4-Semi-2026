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
        this._transactionQueue = [];
        this._transactionInProgress = false;
    }

    async _enqueueTransaction(fn) {
        return new Promise((resolve, reject) => {
            this._transactionQueue.push({ fn, resolve, reject });
            this._processTransactionQueue();
        });
    }

    async _processTransactionQueue() {
        if (this._transactionInProgress || this._transactionQueue.length === 0) return;

        this._transactionInProgress = true;
        const { fn, resolve, reject } = this._transactionQueue.shift();

        try {
            const result = await fn();
            resolve(result);
        } catch (e) {
            reject(e);
        } finally {
            this._transactionInProgress = false;
            // Procesar el próximo item en la cola
            if (this._transactionQueue.length > 0) {
                await new Promise(r => setTimeout(r, 10));
                this._processTransactionQueue();
            }
        }
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
                    console.warn('La respuesta del servidor no incluye los encabezados COOP/COEP necesarios para OPFS.', { coop, coep });
                }
            } catch (e) {
                console.warn('No se pudo verificar encabezados COOP/COEP (no crítico):', e);
            }


            // Inicializar Promiser usando la versión v1 nativa
            const createPromiser = () => new Promise((resolve, reject) => {
                const _promiser = globalThis.sqlite3Worker1Promiser({
                    onready: () => resolve(_promiser),
                    worker: () => new Worker('componentes/sqlite3-worker1.js'),
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

            // Solo intentamos usar OPFS (o su variante sahpool). Si no está disponible, fallamos rápido.
            const preferredVfs = ['opfs-sahpool', 'opfs'];
            const candidates = preferredVfs.filter(v => vfsList.includes(v));
            if (!candidates.length) {
                throw new Error('OPFS no está disponible en este entorno: ' + JSON.stringify(vfsList));
            }

            const savedCid = getSavedIpfsCid();
            let ipfsBytes = null;
            if (savedCid) {
                try {
                    ipfsBytes = await ipfsCat(savedCid);
                } catch {
                    // Ignorar: no es crítico.
                }
            }

            const ensureOpfsPreflight = () => {
                if (!opfsPreflight.isSecureContext) {
                    throw new Error('OPFS requiere un contexto seguro (HTTPS o localhost).');
                }
                if (!opfsPreflight.crossOriginIsolated) {
                    throw new Error(
                        'OPFS (async) requiere cross-origin isolation. Configura COOP/COEP en el servidor.'
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
                ensureOpfsPreflight();

                const filename = `file:academica.sqlite3?vfs=${vfs}`;
                const openArgs = { filename };
                if (ipfsBytes) openArgs.byteArray = ipfsBytes;

                return this.sqlite3('open', openArgs);
            };

            const openResult = await openWithVfs(candidates[0]);

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
                const res = await this.sqlite3('export', {});
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
            this.saveToIpfs().catch(() => { });
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
        
        try {
            await this.dbObj.sqlite3('exec', {
                sql: sql,
                bind: values
            });
            this.dbObj.scheduleSaveToIpfs();
        } catch (e) {
            console.error(`Error en put() para tabla ${this.tableName}:`, e);
            throw e;
        }
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

        // Usar la cola de transacciones para evitar conflictos
        return this.dbObj._enqueueTransaction(async () => {
            try {
                await this.dbObj.sqlite3('exec', { sql: 'BEGIN TRANSACTION' });
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
                try {
                    await this.dbObj.sqlite3('exec', { sql: 'ROLLBACK' });
                } catch (rollbackError) {
                    console.warn('Error en ROLLBACK:', rollbackError);
                }
                console.error('Error en bulkAdd:', e);
                throw e;
            }
        });
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

if (!window.db) {
    window.db = new SQLiteORM();
}
