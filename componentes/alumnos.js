const alumnos = {
    props:['forms'],
    data(){
        return{
            alumno:{
                idAlumno:0,
                codigo:"",
                nombre:"",
                direccion:"",
                email:"",
                telefono:""
            },
            accion:'nuevo',
            idAlumno:0,
            data_alumnos:[]
        }
    },
    methods:{
        buscarAlumno(){
            this.forms.busqueda_alumnos.mostrar = !this.forms.busqueda_alumnos.mostrar;
            this.$emit('buscar');
        },
        modificarAlumno(alumno){
            this.accion = 'modificar';
            this.idAlumno = alumno.idAlumno;
            this.alumno.codigo = alumno.codigo;
            this.alumno.nombre = alumno.nombre;
            this.alumno.direccion = alumno.direccion;
            this.alumno.email = alumno.email;
            this.alumno.telefono = alumno.telefono;
        },
        async guardarAlumno() {
            let datos = {
                idAlumno: this.accion=='modificar' ? this.idAlumno : this.getId(),
                codigo: this.alumno.codigo,
                nombre: this.alumno.nombre,
                direccion: this.alumno.direccion,
                email: this.alumno.email,
                telefono: this.alumno.telefono
            };
            datos.hash = sha256(JSON.stringify(datos)).toString();
            //await this.obtenerAlumnos();

            if(this.data_alumnos.length > 0 && this.accion=='nuevo'){
                alertify.error(`El codigo del alumno ya existe, ${this.data_alumnos[0].nombre}`);
                return; //Termina la ejecucion de la funcion
            }
            try {
                await db.alumnos.put(datos);
                // Recargar la grilla para que se vea el cambio inmediatamente.
                this.$emit('buscar');
            } catch (e) {
                console.error('Error guardando alumno en SQLite (OPFS):', e);
                alertify.error(`Error al guardar alumno en la base local: ${e?.message || e}`);
                return;
            }
            
            // Sincronizar con el servidor con manejo de errores mejorado
            fetch(`private/modulos/alumnos/alumno.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ accion: this.accion, alumnos: datos })
            })
                .then(response=>response.json())
                .then(data=>{
                    if(data.msg === 'ok' || data === true) {
                        console.log('Alumno sincronizado correctamente con el servidor');
                    } else if(data.msg && data.msg.includes('Duplicate entry')) {
                        console.warn('El alumno ya existe en el servidor. Se mantiene la copia local.');
                        alertify.warning(`El alumno ya existe en el servidor. Se actualizará automáticamente.`);
                    } else {
                        alertify.error(`Error al sincronizar con el servidor: ${data.msg || data}`);
                    }
                })
                .catch(err => {
                    console.warn('Error de conectividad al sincronizar alumno:', err);
                    alertify.warning(`No se pudo sincronizar con el servidor. El alumno se guardó localmente.`);
                });
            this.limpiarFormulario();
            alertify.success(`${datos.nombre} guardado correctamente`);
            //this.obtenerAlumnos();
        },
        getId(){
            return new Date().getTime();
        },
        limpiarFormulario(){
            this.accion = 'nuevo';
            this.idAlumno = 0;
            this.alumno.codigo = '';
            this.alumno.nombre = '';
            this.alumno.direccion = '';
            this.alumno.email = '';
            this.alumno.telefono = '';
        },
    },
    template: `
        <div class="row">
            <div class="col-6">
                <form id="frmAlumnos" @submit.prevent="guardarAlumno" @reset.prevent="limpiarFormulario">
                    <div class="card text-bg-dark mb-3" style="max-width: 36rem;">
                        <div class="card-header">REGISTRO DE ALUMNOS</div>
                        <div class="card-body">
                            <div class="row p-1">
                                <div class="col-3">
                                    CODIGO:
                                </div>
                                <div class="col-3">
                                    <input placeholder="codigo" required v-model="alumno.codigo" type="text" class="form-control">
                                </div>
                            </div>
                            <div class="row p-1">
                                <div class="col-3">
                                    NOMBRE:
                                </div>
                                <div class="col-6">
                                    <input placeholder="nombre" required v-model="alumno.nombre" type="text" class="form-control">
                                </div>
                            </div>
                            <div class="row p-1">
                                <div class="col-3">
                                    DIRECCION:
                                </div>
                                <div class="col-9">
                                    <input placeholder="direccion" required v-model="alumno.direccion" type="text" class="form-control">
                                </div>
                            </div>
                            <div class="row p-1">
                                <div class="col-3">
                                    EMAIL:
                                </div>
                                <div class="col-6">
                                    <input placeholder="email" required v-model="alumno.email" type="text" class="form-control">
                                </div>
                            </div>
                            <div class="row p-1">
                                <div class="col-3">
                                    TELEFONO:
                                </div>
                                <div class="col-4">
                                    <input placeholder="telefono" required v-model="alumno.telefono" type="text" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col text-center">
                                    <button type="submit" id="btnGuardarAlumno" class="btn btn-primary">GUARDAR</button>
                                    <button type="reset" id="btnCancelarAlumno" class="btn btn-warning">NUEVO</button>
                                    <button type="button" @click="buscarAlumno" id="btnBuscarAlumno" class="btn btn-success">BUSCAR</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    `
};