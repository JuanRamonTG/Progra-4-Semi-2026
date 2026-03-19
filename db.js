// Gestión de base de datos local usando SQLite WASM con almacenamiento OPFS
const RUTA_BD = 'file:academica.sqlite3?vfs=opfs';

// Definición de columnas por tabla (en orden exacto del schema)
const ESQUEMA_TABLAS = {
    alumnos: {
        llave: 'idAlumno',
        columnas: ['idAlumno', 'codigo', 'nombre', 'direccion', 'email', 'telefono', 'hash'],
        valores: (r) => [r.idAlumno, r.codigo, r.nombre, r.direccion, r.email, r.telefono, r.hash ?? null]
    },
    materias: {
        llave: 'idMateria',
        columnas: ['idMateria', 'codigo', 'nombre', 'uv'],
        valores: (r) => [r.idMateria, r.codigo, r.nombre, r.uv]
    },
    docentes: {
        llave: 'idDocente',
        columnas: ['idDocente', 'codigo', 'nombre', 'direccion', 'email', 'telefono', 'escalafon'],
        valores: (r) => [r.idDocente, r.codigo, r.nombre, r.direccion, r.email, r.telefono, r.escalafon]
    },
    matriculas: {
        llave: 'idMatricula',
        columnas: ['idMatricula', 'codigo', 'fecha', 'idAlumno'],
        valores: (r) => [r.idMatricula, r.codigo, r.fecha, r.idAlumno]
    },
    inscripciones: {
        llave: 'idInscripcion',
        columnas: ['idInscripcion', 'idMatricula', 'idAlumno', 'idMateria', 'ciclo', 'fecha'],
        valores: (r) => [r.idInscripcion, r.idMatricula, r.idAlumno, r.idMateria, r.ciclo, r.fecha]
    }
};

// Genera la interfaz CRUD para una tabla usando su definición de esquema
function crearInterfazTabla(gestorBD, tabla) {
    const def = ESQUEMA_TABLAS[tabla];
    const { llave, columnas, valores } = def;

    const marcadores = columnas.map(() => '?').join(', ');
    const actualizaciones = columnas
        .filter(c => c !== llave)
        .map(c => `${c} = excluded.${c}`)
        .join(', ');

    const sqlUpsert = `
        INSERT INTO ${tabla} (${columnas.join(', ')})
        VALUES (${marcadores})
        ON CONFLICT(${llave}) DO UPDATE SET ${actualizaciones}
    `;

    return {
        guardar:  (r) => gestorBD.ejecutar(sqlUpsert, valores(r)),
        eliminar: (id) => gestorBD.ejecutar(`DELETE FROM ${tabla} WHERE ${llave} = ?`, [id]),
        // Alias para compatibilidad con el resto del código
        put:      function(r)   { return this.guardar(r); },
        delete:   function(id)  { return this.eliminar(id); },
        bulkAdd:  function(rs)  { return Promise.all(rs.map(r => this.guardar(r))); },
        filter: (condicion) => ({
            toArray: async () => {
                const filas = await gestorBD.consultar(`SELECT * FROM ${tabla}`);
                return filas.filter(condicion);
            }
        }),
        orderBy: (campo) => ({
            filter: (condicion) => ({
                toArray: async () => {
                    const filas = await gestorBD.consultar(`SELECT * FROM ${tabla} ORDER BY ${campo} ASC`);
                    return filas.filter(condicion);
                }
            })
        })
    };
}

class GestorBD {
    constructor(comunicador) {
        this._com = comunicador;

        // Tablas del sistema académico
        this.alumnos       = crearInterfazTabla(this, 'alumnos');
        this.materias      = crearInterfazTabla(this, 'materias');
        this.docentes      = crearInterfazTabla(this, 'docentes');
        this.matriculas    = crearInterfazTabla(this, 'matriculas');
        this.inscripciones = crearInterfazTabla(this, 'inscripciones');
    }

    // Ejecuta SQL sin retorno de filas
    async ejecutar(sql, parametros = [], opciones = {}) {
        const respuesta = await this._com('exec', { sql, bind: parametros, ...opciones });
        return respuesta.result;
    }

    // Ejecuta SELECT y devuelve array de objetos
    async consultar(sql, parametros = []) {
        const resultado = await this.ejecutar(sql, parametros, {
            rowMode: 'object',
            resultRows: []
        });
        return resultado.resultRows ?? [];
    }

    // Devuelve solo el primer resultado
    async primero(sql, parametros = []) {
        const filas = await this.consultar(sql, parametros);
        return filas[0] ?? null;
    }

    // Compatibilidad: waitForReady
    async waitForReady() {
        return Promise.resolve();
    }
}

// Crea las tablas del sistema si no existen
async function crearEsquema(bd) {
    await bd.ejecutar(`PRAGMA journal_mode = WAL`);
    await bd.ejecutar(`PRAGMA foreign_keys = ON`);

    await bd.ejecutar(`
        CREATE TABLE IF NOT EXISTS alumnos (
            idAlumno  TEXT PRIMARY KEY,
            codigo    TEXT NOT NULL,
            nombre    TEXT NOT NULL,
            direccion TEXT NOT NULL,
            email     TEXT NOT NULL,
            telefono  TEXT NOT NULL,
            hash      TEXT
        )
    `);
    await bd.ejecutar(`CREATE UNIQUE INDEX IF NOT EXISTS idx_alumnos_codigo ON alumnos(codigo)`);

    await bd.ejecutar(`
        CREATE TABLE IF NOT EXISTS materias (
            idMateria TEXT PRIMARY KEY,
            codigo    TEXT NOT NULL,
            nombre    TEXT NOT NULL,
            uv        TEXT NOT NULL
        )
    `);
    await bd.ejecutar(`CREATE UNIQUE INDEX IF NOT EXISTS idx_materias_codigo ON materias(codigo)`);

    await bd.ejecutar(`
        CREATE TABLE IF NOT EXISTS docentes (
            idDocente TEXT PRIMARY KEY,
            codigo    TEXT NOT NULL,
            nombre    TEXT NOT NULL,
            direccion TEXT NOT NULL,
            email     TEXT NOT NULL,
            telefono  TEXT NOT NULL,
            escalafon TEXT NOT NULL
        )
    `);
    await bd.ejecutar(`CREATE UNIQUE INDEX IF NOT EXISTS idx_docentes_codigo ON docentes(codigo)`);

    await bd.ejecutar(`
        CREATE TABLE IF NOT EXISTS matriculas (
            idMatricula TEXT PRIMARY KEY,
            codigo      TEXT NOT NULL,
            fecha       TEXT NOT NULL,
            idAlumno    TEXT NOT NULL
        )
    `);

    await bd.ejecutar(`
        CREATE TABLE IF NOT EXISTS inscripciones (
            idInscripcion TEXT PRIMARY KEY,
            idMatricula   TEXT NOT NULL,
            idAlumno      TEXT NOT NULL,
            idMateria     TEXT NOT NULL,
            ciclo         TEXT NOT NULL,
            fecha         TEXT NOT NULL
        )
    `);
}

// Arranca el motor SQLite WASM y devuelve la instancia lista
async function arrancarBD() {
    if (!globalThis.sqlite3Worker1Promiser) {
        throw new Error('El módulo SQLite WASM no está disponible en este entorno.');
    }

    // Iniciar el worker y esperar confirmación
    const comunicador = await new Promise((ok, fallo) => {
        const instancia = globalThis.sqlite3Worker1Promiser({
            onready: () => ok(instancia),
            worker:  () => new Worker('componentes/sqlite3-worker1.js'),
            onerror: (e) => fallo(e)
        });
    });

    // Verificar soporte OPFS
    const cfg = await comunicador('config-get', {});
    const vfs = cfg.result?.vfsList ?? [];
    if (!vfs.includes('opfs')) {
        throw new Error('El navegador no soporta OPFS. Se requiere un contexto seguro (HTTPS o localhost).');
    }

    // Abrir o crear el archivo de base de datos
    await comunicador('open', { filename: RUTA_BD });

    const bd = new GestorBD(comunicador);
    await crearEsquema(bd);

    console.log('[BD] Sistema académico listo — SQLite WASM + OPFS activo');
    return bd;
}

// Inicializar y exponer globalmente
globalThis.dbReady = arrancarBD()
    .then((bd) => {
        globalThis.db = bd;
        return bd;
    })
    .catch((error) => {
        console.error('[BD] Fallo al inicializar la base de datos local:', error);
        throw error;
    });
