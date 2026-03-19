const inscripciones = {
    props:['forms'],
    data(){
        return{
            inscripcion:{
                idInscripcion:0,
                idAlumno:"",
                idMateria:"",
                idMatricula:"",
                ciclo:"",
                fecha: new Date().toISOString().slice(0,10)
            },
            accion:'nuevo',
            idInscripcion:0,
            alumnos:[],
            materias:[],
            matriculas:[],
            loadingAlumnos:false,
            loadingMaterias:false
        }
    },
    async mounted(){
        await this.cargarAlumnos();
        await this.cargarMaterias();
        await this.cargarMatriculas();
    },
    methods:{
        async _retryWithBackoff(fn, maxRetries = 3) {
            let lastError;
            for (let attempt = 0; attempt < maxRetries; attempt++) {
                try {
                    return await fn();
                } catch (e) {
                    lastError = e;
                    const delay = Math.min(100 * Math.pow(2, attempt), 1000);
                    console.warn(`Intento ${attempt + 1}/${maxRetries} falló, reintentando en ${delay}ms:`, e.message);
                    await new Promise(r => setTimeout(r, delay));
                }
            }
            throw lastError;
        },
        async cargarAlumnos(){
            this.loadingAlumnos = true;
            try {
                await db.waitForReady();
                this.alumnos = await this._retryWithBackoff(() => db.alumnos.filter(() => true).toArray(), 2);
            } catch (e) {
                console.error('Error leyendo alumnos de la DB local después de reintentos:', e);
                this.alumnos = [];
            }
            if(!this.alumnos.length){
                try {
                    const resp = await fetch(`private/modulos/alumnos/alumno.php?accion=consultar`);
                    const data = await resp.json();
                    this.alumnos = data || [];
                    if(this.alumnos.length) {
                        try {
                            await this._retryWithBackoff(() => db.alumnos.bulkAdd(this.alumnos), 2);
                        } catch (e) {
                            console.warn('Error guardando alumnos en BD local:', e);
                        }
                    }
                } catch (e) {
                    console.error('Error cargando alumnos desde servidor:', e);
                }
            }
            this.loadingAlumnos = false;
        },
        async cargarMaterias(){
            this.loadingMaterias = true;
            try {
                await db.waitForReady();
                this.materias = await this._retryWithBackoff(() => db.materias.filter(() => true).toArray(), 2);
            } catch (e) {
                console.error('Error leyendo materias de la DB local después de reintentos:', e);
                this.materias = [];
            }
            if(!this.materias.length){
                try {
                    const resp = await fetch(`private/modulos/materias/materia.php?accion=consultar`);
                    const data = await resp.json();
                    this.materias = data || [];
                    if(this.materias.length) {
                        try {
                            await this._retryWithBackoff(() => db.materias.bulkAdd(this.materias), 2);
                        } catch (e) {
                            console.warn('Error guardando materias en BD local:', e);
                        }
                    }
                } catch (e) {
                    console.error('Error cargando materias desde servidor:', e);
                }
            }
            this.loadingMaterias = false;
        },
        async cargarMatriculas(){
            try {
                await db.waitForReady();
                this.matriculas = await this._retryWithBackoff(() => db.matriculas.filter(() => true).toArray(), 2);
            } catch (e) {
                console.error('Error leyendo matriculas de la DB local después de reintentos:', e);
                this.matriculas = [];
            }
            if(this.matriculas.length < 1){
                try {
                    const resp = await fetch(`private/modulos/matriculas/matricula.php?accion=consultar`);
                    const data = await resp.json();
                    this.matriculas = data || [];
                    if(this.matriculas.length) {
                        try {
                            await this._retryWithBackoff(() => db.matriculas.bulkAdd(this.matriculas), 2);
                        } catch (e) {
                            console.warn('Error guardando matriculas en BD local:', e);
                        }
                    }
                } catch (e) {
                    console.error('Error cargando matriculas desde servidor:', e);
                }
            }
        },
        buscarInscripcion(){
            this.forms.busqueda_inscripciones.mostrar = !this.forms.busqueda_inscripciones.mostrar;
            this.$emit('buscar');
        },
        modificarInscripcion(inscripcion){
            this.accion = 'modificar';
            this.idInscripcion = inscripcion.idInscripcion;
            this.inscripcion.idAlumno = inscripcion.idAlumno;
            this.inscripcion.idMateria = inscripcion.idMateria;
            this.inscripcion.idMatricula = inscripcion.idMatricula;
            this.inscripcion.ciclo = inscripcion.ciclo;
            this.inscripcion.fecha = inscripcion.fecha;
        },
        async guardarInscripcion() {
            // Asegurarse de tener un idMatricula válido para esta inscripción.
            let idMatricula = this.inscripcion.idMatricula;

            if(!idMatricula){
                // Buscar si ya existe una matrícula para este alumno + ciclo.
                const match = this.matriculas.find(m => m.idAlumno === this.inscripcion.idAlumno && m.codigo === this.inscripcion.ciclo);
                if(match){
                    idMatricula = match.idMatricula;
                } else {
                    // Crear matrícula nueva si no existe.
                    idMatricula = this.getId();
                    const nuevaMatricula = {
                        idMatricula,
                        codigo: this.inscripcion.ciclo,
                        fecha: this.inscripcion.fecha,
                        idAlumno: this.inscripcion.idAlumno
                    };
                    try {
                        await db.matriculas.put(nuevaMatricula);
                        // Sincronizar matrícula con servidor.
                        fetch(`private/modulos/matriculas/matricula.php?accion=nuevo&matriculas=${encodeURIComponent(JSON.stringify(nuevaMatricula))}`)
                            .then(resp=>resp.json())
                            .then(data=>{
                                if(data.msg === 'ok' || data === true) {
                                    console.log('Matrícula sincronizada correctamente');
                                } else if(data.msg && data.msg.includes('Duplicate entry')) {
                                    console.warn('La matrícula automática ya existe en el servidor');
                                } else {
                                    console.warn('No se sincronizó matrícula automáticamente:', data);
                                }
                            })
                            .catch(e=>console.warn('Error sincronizando matrícula:', e));
                        this.matriculas.push(nuevaMatricula);
                    } catch (e) {
                        console.error('Error creando matrícula para la inscripción:', e);
                    }
                }
            }

            let datos = {
                idInscripcion: this.accion=='modificar' ? this.idInscripcion : this.getId(),
                idAlumno: this.inscripcion.idAlumno,
                idMateria: this.inscripcion.idMateria,
                idMatricula,
                ciclo: this.inscripcion.ciclo,
                fecha: this.inscripcion.fecha
            };

            try {
                await db.inscripciones.put(datos);
            } catch (e) {
                console.error('Error guardando inscripcion en SQLite (OPFS):', e);
                alertify.error(`Error al guardar inscripcion en la base local: ${e?.message || e}`);
                return;
            }
            
            // Sincronizar con el servidor con manejo de errores mejorado
            fetch(`private/modulos/inscripciones/inscripcion.php?accion=${this.accion}&inscripciones=${encodeURIComponent(JSON.stringify(datos))}`)
                .then(response=>response.json())
                .then(data=>{
                    if(data.msg === 'ok' || data === true) {
                        console.log('Inscripción sincronizada correctamente con el servidor');
                    } else if(data.msg && data.msg.includes('Duplicate entry')) {
                        console.warn('La inscripción ya existe en el servidor. Se mantiene la copia local.');
                        alertify.warning(`La inscripción ya existe en el servidor. Se actualizará automáticamente.`);
                    } else {
                        alertify.error(`Error al sincronizar con el servidor: ${data.msg || data}`);
                    }
                })
                .catch(err => {
                    console.warn('Error de conectividad al sincronizar inscripción:', err);
                    alertify.warning(`No se pudo sincronizar con el servidor. La inscripción se guardó localmente.`);
                });
            this.limpiarFormulario();
            this.$emit('buscar');
            alertify.success(`Inscripcion guardada correctamente`);
        },
        getId(){
            return new Date().getTime();
        },
        limpiarFormulario(){
            this.accion = 'nuevo';
            this.idInscripcion = 0;
            this.inscripcion.idAlumno = '';
            this.inscripcion.idMateria = '';
            this.inscripcion.idMatricula = '';
            this.inscripcion.ciclo = '';
            this.inscripcion.fecha = new Date().toISOString().slice(0,10);
        },
    },
    template: `
        <div class="row">
            <div class="col-6">
                <form id="frmInscripciones" @submit.prevent="guardarInscripcion" @reset.prevent="limpiarFormulario">
                    <div class="card text-bg-dark mb-3" style="max-width: 36rem;">
                        <div class="card-header">REGISTRO DE INSCRIPCIÓN</div>
                        <div class="card-body">
                            <div class="row p-1">
                                <div class="col-3">
                                    ALUMNO:
                                </div>
                                <div class="col-9">
                                    <select required v-model="inscripcion.idAlumno" class="form-select">
                                        <option value="" disabled>{{ loadingAlumnos ? 'Cargando alumnos...' : (alumnos.length ? '-- Buscar alumno --' : 'No hay alumnos disponibles') }}</option>
                                        <option v-for="alumno in alumnos" :key="alumno.idAlumno" :value="alumno.idAlumno">
                                            {{ alumno.codigo }} - {{ alumno.nombre }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="row p-1">
                                <div class="col-3">
                                    MATERIA:
                                </div>
                                <div class="col-9">
                                    <select required v-model="inscripcion.idMateria" class="form-select">
                                        <option value="" disabled>{{ loadingMaterias ? 'Cargando materias...' : (materias.length ? '-- Seleccione una materia --' : 'No hay materias disponibles') }}</option>
                                        <option v-for="materia in materias" :key="materia.idMateria" :value="materia.idMateria">
                                            {{ materia.codigo }} - {{ materia.nombre }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="row p-1">
                                <div class="col-3">
                                    CICLO:
                                </div>
                                <div class="col-6">
                                    <input placeholder="Ej: 2026-1" required v-model="inscripcion.ciclo" type="text" class="form-control">
                                </div>
                            </div>
                            <div class="row p-1">
                                <div class="col-3">
                                    FECHA:
                                </div>
                                <div class="col-6">
                                    <input placeholder="AAAA-MM-DD" required v-model="inscripcion.fecha" type="date" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col text-center">
                                    <button type="submit" id="btnGuardarInscripcion" class="btn btn-primary">GUARDAR</button>
                                    <button type="reset" id="btnCancelarInscripcion" class="btn btn-warning">NUEVO</button>
                                    <button type="button" @click="buscarInscripcion" id="btnBuscarInscripcion" class="btn btn-success">BUSCAR</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    `
};
