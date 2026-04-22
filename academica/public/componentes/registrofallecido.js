const registrofallecido = {
    props:['forms'],
    data(){
        return{
            registrofallecido:{
                id:0,
                fecha_hora:"",
                ubicacion:"",
                descripcion:"",
                verificacion:false,
                fotos:"",
                testigos:"",
                hora_fallecimiento:"",
                estado:""
            },
            accion:'nuevo',
            id:0,
            data_registrofallecidos:[]
        }
    },
    methods:{
        buscarRegistroFallecido(){
            this.forms.busqueda_registrofallecido.mostrar = !this.forms.busqueda_registrofallecido.mostrar;
            this.$emit('buscar');
        },
        modificarRegistroFallecido(registro){
            this.accion = 'modificar';
            this.id = registro.id;
            this.registrofallecido.fecha_hora = registro.fecha_hora;
            this.registrofallecido.ubicacion = registro.ubicacion;
            this.registrofallecido.descripcion = registro.descripcion;
            this.registrofallecido.verificacion = registro.verificacion;
            this.registrofallecido.fotos = registro.fotos;
            this.registrofallecido.testigos = registro.testigos;
            this.registrofallecido.hora_fallecimiento = registro.hora_fallecimiento;
            this.registrofallecido.estado = registro.estado;
        },
        async guardarRegistroFallecido() {
            let datos = {
                id: this.accion=='modificar' ? this.id : null,
                fecha_hora: this.registrofallecido.fecha_hora,
                ubicacion: this.registrofallecido.ubicacion,
                descripcion: this.registrofallecido.descripcion,
                verificacion: this.registrofallecido.verificacion,
                fotos: this.registrofallecido.fotos,
                testigos: this.registrofallecido.testigos,
                hora_fallecimiento: this.registrofallecido.hora_fallecimiento,
                estado: this.registrofallecido.estado
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
                let response = await fetch((window.assetBase || '') + `private/modulos/registrofallecido/registrofallecido.php`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ accion: this.accion, registrofallecido: datos })
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
        async eliminarRegistroFallecido(id) {
            if(!confirm('¿Está seguro de que desea eliminar este registro?')) {
                return;
            }

            try {
                let response = await fetch((window.assetBase || '') + `private/modulos/registrofallecido/registrofallecido.php`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ accion: 'eliminar', registrofallecido: {id: id} })
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
        getEstadoColor(estado){
            if(estado === 'fallecido') return 'danger';
            if(estado === 'hospitalizado') return 'warning';
            return 'secondary';
        },
        limpiarFormulario(){
            this.accion = 'nuevo';
            this.id = 0;
            this.registrofallecido.fecha_hora = '';
            this.registrofallecido.ubicacion = '';
            this.registrofallecido.descripcion = '';
            this.registrofallecido.verificacion = false;
            this.registrofallecido.fotos = '';
            this.registrofallecido.testigos = '';
            this.registrofallecido.hora_fallecimiento = '';
            this.registrofallecido.estado = '';
        },
    },
    template: `
        <div class="row">
            <div class="col-lg-6">
                <form id="frmRegistroFallecido" @submit.prevent="guardarRegistroFallecido" @reset.prevent="limpiarFormulario">
                    <div class="card text-bg-danger mb-3">
                        <div class="card-header fw-bold">
                            <i class="fas fa-heartbeat"></i> REGISTRO DE FALLECIDO
                        </div>
                        <div class="card-body text-dark">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Fecha y Hora del evento:</label>
                                <input type="datetime-local" required v-model="registrofallecido.fecha_hora" class="form-control form-control-lg">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Ubicación:</label>
                                <input placeholder="Ej: Hospital, Domicilio" required v-model="registrofallecido.ubicacion" type="text" class="form-control form-control-lg">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Descripción:</label>
                                <textarea placeholder="Describe los detalles del evento" v-model="registrofallecido.descripcion" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Hora del fallecimiento:</label>
                                <input type="datetime-local" v-model="registrofallecido.hora_fallecimiento" class="form-control form-control-lg">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Testigos:</label>
                                <textarea placeholder="Nombres de testigos" v-model="registrofallecido.testigos" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Estado:</label>
                                <select v-model="registrofallecido.estado" class="form-select form-select-lg">
                                    <option value="">Seleccionar...</option>
                                    <option value="fallecido">Fallecido</option>
                                    <option value="hospitalizado">Hospitalizado</option>
                                    <option value="otro">Otro</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Fotos (URL):</label>
                                <input placeholder="URL de las fotos" v-model="registrofallecido.fotos" type="text" class="form-control form-control-lg">
                            </div>
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" v-model="registrofallecido.verificacion" id="verificacionFallecido">
                                    <label class="form-check-label fw-bold" for="verificacionFallecido">
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
                                <button type="button" @click="buscarRegistroFallecido" class="btn btn-info btn-lg">
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
                        <i class="fas fa-list"></i> LISTADO DE REGISTROS
                    </div>
                    <div class="card-body" style="max-height: 700px; overflow-y: auto;">
                        <div v-if="data_registrofallecidos.length === 0" class="alert alert-info">
                            No hay registros disponibles
                        </div>
                        <div v-for="item in data_registrofallecidos" :key="item.id" class="card mb-2 border-start border-danger border-4">
                            <div class="card-body p-2">
                                <div class="row">
                                    <div class="col-md-8">
                                        <p class="mb-1"><strong>Ubicación:</strong> {{ item.ubicacion }}</p>
                                        <p class="mb-1"><strong>Fecha:</strong> {{ new Date(item.fecha_hora).toLocaleString() }}</p>
                                        <p class="mb-1"><strong>Descripción:</strong> {{ item.descripcion || 'N/A' }}</p>
                                        <p class="mb-1"><strong>Testigos:</strong> {{ item.testigos || 'N/A' }}</p>
                                        <p class="mb-1">
                                            <strong>Estado:</strong> 
                                            <span :class="'badge bg-' + getEstadoColor(item.estado)">
                                                {{ item.estado || 'N/A' }}
                                            </span>
                                        </p>
                                        <p class="mb-1">
                                            <span v-if="item.verificacion" class="badge bg-success">
                                                <i class="fas fa-check"></i> Verificado
                                            </span>
                                            <span v-else class="badge bg-warning">Pendiente</span>
                                        </p>
                                    </div>
                                    <div class="col-md-4">
                                        <button @click="modificarRegistroFallecido(item)" class="btn btn-sm btn-primary mb-2 w-100">
                                            <i class="fas fa-edit"></i> Editar
                                        </button>
                                        <button @click="eliminarRegistroFallecido(item.id)" class="btn btn-sm btn-danger w-100">
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
