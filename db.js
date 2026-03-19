import './componentes/sqlite3-worker1-promiser-bundler-friendly.js';

class SQLiteORM {
    constructor() {
        this.sqlite3 = null;
        this.alumnos = new Table(this, 'alumnos', 'idAlumno');
        this.materias = new Table(this, 'materias', 'idMateria');
        this.docentes = new Table(this, 'docentes', 'idDocente');
        this.matriculas = new Table(this, 'matriculas', 'idMatricula');
        this.inscripciones = new Table(this, 'inscripciones', 'idInscripcion');
        this.isReady = false;
        this._readyPromise = this.init();
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
            console.log('OPFS preflight:', opfsPreflight);

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

            // Inicializar Promiser que corre en un Web Worker
            const promiser = await new Promise((resolve, reject) => {
                const _promiser = self.sqlite3Worker1Promiser({
                    onready: () => resolve(_promiser),
                    worker: () => new Worker(new URL('./componentes/sqlite3-worker1-bundler-friendly.mjs', import.meta.url), { type: 'module' }),
                    onerror: (err) => reject(err)
                });
            });

            this.sqlite3 = promiser;

            // Consultar la configuración del worker para saber qué VFS de OPFS está disponible
            const conf = await this.sqlite3('config-get', {});
            const vfsList = conf?.result?.vfsList || [];
            console.log('VFS disponibles en el Worker:', vfsList);

            let opfsVfs = null;
            if (vfsList.includes('opfs-sahpool')) {
                opfsVfs = 'opfs-sahpool';
            } else if (vfsList.includes('opfs')) {
                opfsVfs = 'opfs';
            } else {
                throw new Error('Ninguna versión de OPFS (opfs u opfs-sahpool) está disponible en este navegador/entorno.');
            }

            // Abrir base de datos OPFS
            // En sqlite3-wasm, el nombre con query `?vfs=...` suele ser la forma más consistente.
            // (El proyecto que funciona usa este patrón).
            const dbFilename = `file:academica.sqlite3?vfs=${opfsVfs}`;
            console.log('Abriendo base de datos usando VFS:', opfsVfs, 'filename:', dbFilename);
            const openRes = await this.sqlite3('open', { filename: dbFilename });
            console.log('Resultado de open (OPFS):', openRes);

            // Crear las tablas
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
            console.log('Esquema OPFS cargado correctamente en Web Worker Asincrónico.');
            this.isReady = true;
        } catch (err) {
            const errInfo = err instanceof Error
                ? { name: err.name, message: err.message, stack: err.stack }
                : err;
            console.error('Error inicializando SQLite OPFS Promiser:', errInfo);
            // Importante: que el flujo "waitForReady" falle en vez de continuar a medias.
            this.isReady = false;
            throw err;
        }
    }

    async waitForReady() {
        if (!this.isReady) {
            await this._readyPromise;
        }
        if (!this.isReady) {
            throw new Error('SQLiteORM no está listo: falló la inicialización (OPFS).');
        }
    }
}

class Table {
    constructor(dbObj, tableName, pk) {
        this.dbObj = dbObj;
        this.tableName = tableName;
        this.pk = pk;
    }

    async getTableInfo() {
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
    }

    async delete(id) {
        await this.dbObj.waitForReady();
        await this.dbObj.sqlite3('exec', {
            sql: `DELETE FROM ${this.tableName} WHERE ${this.pk} = ?`,
            bind: [id]
        });
    }

    async bulkAdd(items) {
        await this.dbObj.waitForReady();
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
        } catch (e) {
            await this.dbObj.sqlite3('exec', { sql: 'ROLLBACK' });
            console.error('Rollback en bulkAdd', e);
        }
    }

    filter(predicate) {
        return {
            toArray: async () => {
                await this.dbObj.waitForReady();
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
