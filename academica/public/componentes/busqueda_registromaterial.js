const busqueda_registromaterial = {
    props:['forms'],
    data(){
        return{
            busqueda:{
                ubicacion: '',
                descripcion: '',
                fecha_desde: '',
                fecha_hasta: '',
                verificacion: ''
            },
            mostrar: false
        }
    },
    methods:{
        async buscar() {
            let params = new URLSearchParams();
            params.append('accion', 'consultar');
            
            if(this.busqueda.ubicacion) {
                params.append('ubicacion', this.busqueda.ubicacion);
            }
            if(this.busqueda.descripcion) {
                params.append('descripcion', this.busqueda.descripcion);
            }
            if(this.busqueda.fecha_desde) {
                params.append('fecha_desde', this.busqueda.fecha_desde);
            }
            if(this.busqueda.fecha_hasta) {
                params.append('fecha_hasta', this.busqueda.fecha_hasta);
            }
            if(this.busqueda.verificacion !== '') {
                params.append('verificacion', this.busqueda.verificacion);
            }

            try {
                let response = await fetch((window.assetBase || '') + `private/modulos/registromaterial/registromaterial.php?${params}`, {
                    method: 'GET',
                    headers: { 'Content-Type': 'application/json' }
                });
                let data = await response.json();
                this.$parent.registromaterial.data_registromateriales = data;
                
                if(data.length === 0) {
                    alertify.warning('No se encontraron registros');
                } else {
                    alertify.success(`Se encontraron ${data.length} registro(s)`);
                }
            } catch(err) {
                console.error('Error:', err);
                alertify.error('Error al buscar: ' + err.message);
            }
        },
        limpiarBusqueda(){
            this.busqueda = {
                ubicacion: '',
                descripcion: '',
                fecha_desde: '',
                fecha_hasta: '',
                verificacion: ''
            };
        }
    },
    template: `
        <div v-if="mostrar" class="card card-outline card-primary mb-3">
            <div class="card-header">
                <h3 class="card-title fw-bold"><i class="fas fa-search"></i> BUSCAR REGISTRO DE MATERIAL</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Ubicación:</label>
                        <input type="text" v-model="busqueda.ubicacion" placeholder="Buscar por ubicación" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Descripción:</label>
                        <input type="text" v-model="busqueda.descripcion" placeholder="Buscar por descripción" class="form-control">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Fecha Desde:</label>
                        <input type="date" v-model="busqueda.fecha_desde" class="form-control">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Fecha Hasta:</label>
                        <input type="date" v-model="busqueda.fecha_hasta" class="form-control">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Verificación:</label>
                        <select v-model="busqueda.verificacion" class="form-select">
                            <option value="">Todos</option>
                            <option value="1">Verificado</option>
                            <option value="0">Pendiente</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button @click="buscar" class="btn btn-primary">
                    <i class="fas fa-search"></i> Buscar
                </button>
                <button @click="limpiarBusqueda" class="btn btn-warning ms-2">
                    <i class="fas fa-eraser"></i> Limpiar
                </button>
            </div>
        </div>
    `
};
