const registromaterial = {
    props:['forms'],
    data(){
        return{
            registromaterial:{
                id:0,
                fecha_hora:"",
                ubicacion:"",
                descripcion:"",
                verificacion:false,
                fotos:""
            },
            accion:'nuevo',
            id:0,
            data_registromateriales:[]
        }
    },
    methods:{
        buscarRegistroMaterial(){
            this.forms.busqueda_registromaterial.mostrar = !this.forms.busqueda_registromaterial.mostrar;
            this.$emit('buscar');
        },
        modificarRegistroMaterial(material){
            this.accion = 'modificar';
            this.id = material.id;
            this.registromaterial.fecha_hora = material.fecha_hora;
            this.registromaterial.ubicacion = material.ubicacion;
            this.registromaterial.descripcion = material.descripcion;
            this.registromaterial.verificacion = material.verificacion;
            this.registromaterial.fotos = material.fotos;
        },
        async guardarRegistroMaterial() {
            let datos = {
                id: this.accion=='modificar' ? this.id : null,
                fecha_hora: this.registromaterial.fecha_hora,
                ubicacion: this.registromaterial.ubicacion,
                descripcion: this.registromaterial.descripcion,
                verificacion: this.registromaterial.verificacion,
                fotos: this.registromaterial.fotos
            };

            // Validaciones
            if(!datos.fecha_hora) {
                alertify.error('La fecha y hora es requerida');
                return;
            }
            if(!datos.ubicacion) {
                alertify.error('La ubicación es requerida');
                return;
            }

            try {
                let response = await fetch((window.assetBase || '') + `private/modulos/registromaterial/registromaterial.php`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ accion: this.accion, registromaterial: datos })
                });
                let data = await response.json();
                
                if(data.success) {
                    alertify.success(data.msg);
                    this.$emit('buscar');
                    this.limpiarFormulario();
                } else {
                    alertify.error(data.msg || 'Error al guardar el registro');
                }
            } catch(err) {
                console.error('Error:', err);
                alertify.error('Error al guardar el registro: ' + err.message);
            }
        },
        async eliminarRegistroMaterial(id) {
            if(!confirm('¿Está seguro de que desea eliminar este registro?')) {
                return;
            }

            try {
                let response = await fetch((window.assetBase || '') + `private/modulos/registromaterial/registromaterial.php`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ accion: 'eliminar', registromaterial: {id: id} })
                });
                let data = await response.json();
                
                if(data.success) {
                    alertify.success(data.msg);
                    this.$emit('buscar');
                } else {
                    alertify.error(data.msg || 'Error al eliminar el registro');
                }
            } catch(err) {
                console.error('Error:', err);
                alertify.error('Error al eliminar: ' + err.message);
            }
        },
        limpiarFormulario(){
            this.accion = 'nuevo';
            this.id = 0;
            this.registromaterial.fecha_hora = '';
            this.registromaterial.ubicacion = '';
            this.registromaterial.descripcion = '';
            this.registromaterial.verificacion = false;
            this.registromaterial.fotos = '';
        },
    },
    template: `
        <div class="row">
            <div class="col-lg-6">
                <form id="frmRegistroMaterial" @submit.prevent="guardarRegistroMaterial" @reset.prevent="limpiarFormulario">
                    <div class="card text-bg-primary mb-3">
                        <div class="card-header fw-bold">
                            <i class="fas fa-box-open"></i> REGISTRO DE MATERIAL
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Fecha y Hora:</label>
                                <input type="datetime-local" required v-model="registromaterial.fecha_hora" class="form-control form-control-lg">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Ubicación:</label>
                                <input placeholder="Ej: Sala 1, Almacén A" required v-model="registromaterial.ubicacion" type="text" class="form-control form-control-lg">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Descripción:</label>
                                <textarea placeholder="Describe el material" v-model="registromaterial.descripcion" class="form-control" rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Fotos (URL):</label>
                                <input placeholder="URL de las fotos" v-model="registromaterial.fotos" type="text" class="form-control form-control-lg">
                            </div>
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" v-model="registromaterial.verificacion" id="verificacionMaterial">
                                    <label class="form-check-label fw-bold" for="verificacionMaterial">
                                        Verificado
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex gap-2 justify-content-center">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-save"></i> GUARDAR
                                </button>
                                <button type="reset" class="btn btn-warning btn-lg">
                                    <i class="fas fa-times"></i> LIMPIAR
                                </button>
                                <button type="button" @click="buscarRegistroMaterial" class="btn btn-info btn-lg">
                                    <i class="fas fa-search"></i> BUSCAR
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-lg-6">
                <div class="card text-bg-light">
                    <div class="card-header fw-bold">
                        <i class="fas fa-list"></i> LISTADO DE MATERIALES
                    </div>
                    <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                        <div v-if="data_registromateriales.length === 0" class="alert alert-info">
                            No hay registros disponibles
                        </div>
                        <div v-for="item in data_registromateriales" :key="item.id" class="card mb-2 border-start border-primary border-4">
                            <div class="card-body p-2">
                                <div class="row">
                                    <div class="col-md-8">
                                        <p class="mb-1"><strong>Ubicación:</strong> {{ item.ubicacion }}</p>
                                        <p class="mb-1"><strong>Fecha:</strong> {{ new Date(item.fecha_hora).toLocaleString() }}</p>
                                        <p class="mb-1"><strong>Descripción:</strong> {{ item.descripcion || 'N/A' }}</p>
                                        <p class="mb-1">
                                            <strong>Estado:</strong> 
                                            <span v-if="item.verificacion" class="badge bg-success">
                                                <i class="fas fa-check"></i> Verificado
                                            </span>
                                            <span v-else class="badge bg-warning">Pendiente</span>
                                        </p>
                                    </div>
                                    <div class="col-md-4">
                                        <button @click="modificarRegistroMaterial(item)" class="btn btn-sm btn-primary mb-2 w-100">
                                            <i class="fas fa-edit"></i> Editar
                                        </button>
                                        <button @click="eliminarRegistroMaterial(item.id)" class="btn btn-sm btn-danger w-100">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `
};
