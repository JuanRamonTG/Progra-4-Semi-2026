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
            ciclos:[]
        }
    },
    async mounted(){
        await this.cargarAlumnos();
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
        async cargarMatriculas(){
            this.data_matriculas = await db.matriculas.filter(() => true).toArray();
            if(this.data_matriculas.length < 1){
                const resp = await fetch(`private/modulos/matriculas/matricula.php?accion=consultar`);
                const data = await resp.json();
                this.data_matriculas = data;
                await db.matriculas.bulkAdd(data);
            }
            this.refreshCiclos();
        },
        refreshCiclos(){
            const unique = new Set(this.data_matriculas.map(m => m.codigo).filter(v => !!v));
            this.ciclos = Array.from(unique).sort();
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
            fetch(`private/modulos/matriculas/matricula.php?accion=${this.accion}&matriculas=${JSON.stringify(datos)}`)
                .then(response=>response.json())
                .then(data=>{
                    if(data!=true) alertify.error(`Error al sincronizar con el servidor: ${data}`);
                });
            await this.cargarMatriculas();
            this.limpiarFormulario();
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
                                        <option value="">-- Buscar alumno --</option>
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
                                    <select required v-model="matricula.codigo" class="form-select">
                                        <option value="">-- Seleccione ciclo --</option>
                                        <option v-for="ciclo in ciclos" :key="ciclo" :value="ciclo">
                                            {{ ciclo }}
                                        </option>
                                    </select>
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
