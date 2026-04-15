const materias = {
    props:['forms'],
    data(){
        return{
            materia:{
                idMateria:0,
                codigo:"",
                nombre:"",
                uv:'',
            },
            accion:'nuevo',
            idMateria:0,
            data_materias:[]
        }
    },
    methods:{
        buscarMateria(){
            this.forms.busqueda_materias.mostrar = !this.forms.busqueda_materias.mostrar;
            this.$emit('buscar');
        },
        modificarMateria(materia){
            this.accion = 'modificar';
            this.idMateria = materia.idMateria;
            this.materia.codigo = materia.codigo;
            this.materia.nombre = materia.nombre;
            this.materia.uv = materia.uv;
        },
        async guardarMateria() {
            let datos = {
                idMateria: this.accion=='modificar' ? this.idMateria : this.getId(),
                codigo: this.materia.codigo,
                nombre: this.materia.nombre,
                uv: this.materia.uv,
            };

            try {
                await db.materias.put(datos);
            } catch (e) {
                console.error('Error guardando materia en SQLite (OPFS):', e);
                alertify.error(`Error al guardar materia en la base local: ${e?.message || e}`);
                return;
            }

            fetch((window.assetBase || '') + `private/modulos/materias/materia.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ accion: this.accion, materias: datos })
            })
                .then(response=>response.json())
                .then(data=>{
                    if(data === true || data.msg === 'ok') {
                        console.log('Materia sincronizada correctamente con el servidor');
                    } else {
                        // data puede ser string (error SQL directo) o {msg: '...'}
                        const errorMsg = (typeof data === 'string') ? data : (data.msg || JSON.stringify(data));
                        console.error('Error servidor:', errorMsg);
                        alertify.error(`Error al sincronizar: ${errorMsg}`);
                    }
                })
                .catch(err => {
                    console.warn('Error de conectividad al sincronizar materia:', err);
                    alertify.warning(`No se pudo sincronizar con el servidor. La materia se guardó localmente.`);
                });

            this.limpiarFormulario();
            this.$emit('buscar');
            alertify.success(`Materia ${datos.nombre} guardada correctamente`);
        },
        getId(){
            return new Date().getTime();
        },
        limpiarFormulario(){
            this.accion = 'nuevo';
            this.idMateria = 0;
            this.materia.codigo = '';
            this.materia.nombre = '';
            this.materia.uv = '';
        },
    },
    template: `
        <div class="row">
            <div class="col-6">
                <form id="frmMaterias" @submit.prevent="guardarMateria" @reset.prevent="limpiarFormulario">
                    <div class="card text-bg-dark mb-3" style="max-width: 36rem;">
                        <div class="card-header">REGISTRO DE MATERIAS</div>
                        <div class="card-body">
                            <div class="row p-1">
                                <div class="col-3">
                                    CODIGO:
                                </div>
                                <div class="col-3">
                                    <input placeholder="codigo" required v-model="materia.codigo" type="text" class="form-control">
                                </div>
                            </div>
                            <div class="row p-1">
                                <div class="col-3">
                                    NOMBRE:
                                </div>
                                <div class="col-6">
                                    <input placeholder="nombre" required v-model="materia.nombre" type="text" class="form-control">
                                </div>
                            </div>
                            <div class="row p-1">
                                <div class="col-3">
                                    UV:
                                </div>
                                <div class="col-9">
                                    <input placeholder="uv" required v-model="materia.uv" type="text" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col text-center">
                                    <button type="submit" id="btnGuardarMateria" class="btn btn-primary">GUARDAR</button>
                                    <button type="reset" id="btnCancelarMateria" class="btn btn-warning">NUEVO</button>
                                    <button type="button" @click="buscarMateria" id="btnBuscarMateria" class="btn btn-success">BUSCAR</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    `
};