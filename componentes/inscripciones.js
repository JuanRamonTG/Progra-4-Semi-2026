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
            ciclos:[]
        }
    },
    async mounted(){
        await this.cargarAlumnos();
        await this.cargarMaterias();
        await this.cargarMatriculas();
    },
    methods:{
        async cargarAlumnos(){
            this.alumnos = await db.alumnos.filter(() => true).toArray();
            if(this.alumnos.length < 1){
                const resp = await fetch(`private/modulos/alumnos/alumno.php?accion=consultar`);
                const data = await resp.json();
                this.alumnos = data;
                await db.alumnos.bulkAdd(data);
            }
        },
        async cargarMaterias(){
            this.materias = await db.materias.filter(() => true).toArray();
            if(this.materias.length < 1){
                const resp = await fetch(`private/modulos/materias/materia.php?accion=consultar`);
                const data = await resp.json();
                this.materias = data;
                await db.materias.bulkAdd(data);
            }
        },
        async cargarMatriculas(){
            this.matriculas = await db.matriculas.filter(() => true).toArray();
            if(this.matriculas.length < 1){
                const resp = await fetch(`private/modulos/matriculas/matricula.php?accion=consultar`);
                const data = await resp.json();
                this.matriculas = data;
                await db.matriculas.bulkAdd(data);
            }
            this.refreshCiclos();
        },
        refreshCiclos(){
            let filtered = this.matriculas;
            if(this.inscripcion.idAlumno){
                filtered = filtered.filter(m => m.idAlumno === this.inscripcion.idAlumno);
            }
            const unique = new Set(filtered.map(m => m.codigo).filter(v => !!v));
            this.ciclos = Array.from(unique).sort();
        },
        buscarInscripcion(){
            this.forms.busqueda_inscripciones.mostrar = !this.forms.busqueda_inscripciones.mostrar;
            this.$emit('buscar');
        },
        onAlumnoChange(){
            this.inscripcion.ciclo = '';
            this.inscripcion.idMatricula = '';
            this.inscripcion.fecha = new Date().toISOString().slice(0,10);
            this.refreshCiclos();
        },
        onCicloChange(){
            const matricula = this.matriculas.find(m => m.idAlumno === this.inscripcion.idAlumno && m.codigo === this.inscripcion.ciclo);
            if(matricula){
                this.inscripcion.idMatricula = matricula.idMatricula;
                this.inscripcion.fecha = matricula.fecha;
            }
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
            let datos = {
                idInscripcion: this.accion=='modificar' ? this.idInscripcion : this.getId(),
                idAlumno: this.inscripcion.idAlumno,
                idMateria: this.inscripcion.idMateria,
                idMatricula: this.inscripcion.idMatricula,
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
            fetch(`private/modulos/inscripciones/inscripcion.php?accion=${this.accion}&inscripciones=${JSON.stringify(datos)}`)
                .then(response=>response.json())
                .then(data=>{
                    if(data!=true) alertify.error(`Error al sincronizar con el servidor: ${data}`);
                });
            this.limpiarFormulario();
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
                                    <select required v-model="inscripcion.idAlumno" @change="onAlumnoChange" class="form-select">
                                        <option value="">-- Buscar alumno --</option>
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
                                        <option value="">-- Seleccione una materia --</option>
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
                                    <select required v-model="inscripcion.ciclo" @change="onCicloChange" class="form-select">
                                        <option value="">-- Seleccione ciclo --</option>
                                        <option v-for="ciclo in ciclos" :key="ciclo" :value="ciclo">
                                            {{ ciclo }}
                                        </option>
                                    </select>
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
