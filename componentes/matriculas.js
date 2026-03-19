const matriculas = {
    props:['forms'],
    data(){
        return{
            matricula:{
                idMatricula:0,
                codigo:"",
                fecha:new Date().toISOString().slice(0,10),
                idAlumno:""
            },
            accion:'nuevo',
            idMatricula:0,
            data_matriculas:[],
            alumnos:[],
            loadingAlumnos:false
        }
    },
    async mounted(){
        await this.cargarAlumnos();
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
        async cargarMatriculas(){
            try {
                await db.waitForReady();
                this.data_matriculas = await this._retryWithBackoff(() => db.matriculas.filter(() => true).toArray(), 2);
            } catch (e) {
                console.error('Error leyendo matriculas de la DB local después de reintentos:', e);
                this.data_matriculas = [];
            }
            if(this.data_matriculas.length < 1){
                try {
                    const resp = await fetch(`private/modulos/matriculas/matricula.php?accion=consultar`);
                    const data = await resp.json();
                    this.data_matriculas = data || [];
                    if(this.data_matriculas.length) {
                        try {
                            await this._retryWithBackoff(() => db.matriculas.bulkAdd(this.data_matriculas), 2);
                        } catch (e) {
                            console.warn('Error guardando matriculas en BD local:', e);
                        }
                    }
                } catch (e) {
                    console.error('Error cargando matriculas desde servidor:', e);
                    this.data_matriculas = [];
                }
            }
        },
        buscarMatricula(){
            this.forms.busqueda_matriculas.mostrar = !this.forms.busqueda_matriculas.mostrar;
            this.$emit('buscar');
        },
        modificarMatricula(matricula){
            this.accion = 'modificar';
            this.idMatricula = matricula.idMatricula;
            this.matricula.codigo = matricula.codigo;
            this.matricula.fecha = matricula.fecha;
            this.matricula.idAlumno = matricula.idAlumno;
        },
        async guardarMatricula() {
            let datos = {
                idMatricula: this.accion=='modificar' ? this.idMatricula : this.getId(),
                codigo: this.matricula.codigo,
                fecha: this.matricula.fecha,
                idAlumno: this.matricula.idAlumno
            };

            try {
                await db.matriculas.put(datos);
            } catch (e) {
                console.error('Error guardando matricula en SQLite (OPFS):', e);
                alertify.error(`Error al guardar matricula en la base local: ${e?.message || e}`);
                return;
            }
            
            // Sincronizar con el servidor con manejo de errores mejorado
            fetch(`private/modulos/matriculas/matricula.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ accion: this.accion, matriculas: datos })
            })
                .then(response=>response.json())
                .then(data=>{
                    if(data.msg === 'ok' || data === true) {
                        console.log('Matricula sincronizada correctamente con el servidor');
                    } else if(data.msg && data.msg.includes('Duplicate entry')) {
                        console.warn('La matricula ya existe en el servidor. Se mantiene la copia local.');
                        alertify.warning(`La matrícula ya existe en el servidor. Se actualizará automáticamente.`);
                    } else {
                        alertify.error(`Error al sincronizar con el servidor: ${data.msg || data}`);
                    }
                })
                .catch(err => {
                    console.warn('Error de conectividad al sincronizar matrícula:', err);
                    alertify.warning(`No se pudo sincronizar con el servidor. La matrícula se guardó localmente.`);
                });
            
            await this.cargarMatriculas();
            this.limpiarFormulario();
            this.$emit('buscar');
            alertify.success(`Matricula guardada correctamente`);
        },
        getId(){
            return new Date().getTime();
        },
        limpiarFormulario(){
            this.accion = 'nuevo';
            this.idMatricula = 0;
            this.matricula.codigo = '';
            this.matricula.fecha = new Date().toISOString().slice(0,10);
            this.matricula.idAlumno = '';
        },
    },
    template: `
        <div class="row">
            <div class="col-6">
                <form id="frmMatriculas" @submit.prevent="guardarMatricula" @reset.prevent="limpiarFormulario">
                    <div class="card text-bg-dark mb-3" style="max-width: 36rem;">
                        <div class="card-header">REGISTRO DE MATRICULA</div>
                        <div class="card-body">
                            <div class="row p-1">
                                <div class="col-3">
                                    ALUMNO:
                                </div>
                                <div class="col-9">
                                    <select required v-model="matricula.idAlumno" class="form-select">
                                        <option value="" disabled>{{ loadingAlumnos ? 'Cargando alumnos...' : (alumnos.length ? '-- Buscar alumno --' : 'No hay alumnos disponibles') }}</option>
                                        <option v-for="alumno in alumnos" :key="alumno.idAlumno" :value="alumno.idAlumno">
                                            {{ alumno.codigo }} - {{ alumno.nombre }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="row p-1">
                                <div class="col-3">
                                    FECHA DE MATRICULA:
                                </div>
                                <div class="col-6">
                                    <input placeholder="AAAA-MM-DD" required v-model="matricula.fecha" type="date" class="form-control">
                                </div>
                            </div>
                            <div class="row p-1">
                                <div class="col-3">
                                    CICLO ACADÉMICO:
                                </div>
                                <div class="col-6">
                                    <input placeholder="Ej: 2026-1" required v-model="matricula.codigo" type="text" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col text-center">
                                    <button type="submit" id="btnGuardarMatricula" class="btn btn-primary">GUARDAR</button>
                                    <button type="reset" id="btnCancelarMatricula" class="btn btn-warning">NUEVO</button>
                                    <button type="button" @click="buscarMatricula" id="btnBuscarMatricula" class="btn btn-success">BUSCAR</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    `
};
