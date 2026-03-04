const busqueda_autores = {
    data() {
        return {
            buscar: '',
            autores: []
        }
    },
    methods: {
        modificarAutor(autor) {
            this.$emit('modificar', autor);
        },
        async obtenerAutores() {
            this.autores = await db.autores.filter(
                autor => autor.codigo.toLowerCase().includes(this.buscar.toLowerCase())
                    || autor.nombre.toLowerCase().includes(this.buscar.toLowerCase())
            ).toArray();
        },
        async eliminarAutor(autor, e) {
            e.stopPropagation();
            alertify.confirm('Eliminar autor', `¿Está seguro de eliminar a ${autor.nombre}?`, async e => {
                await db.autores.delete(autor.idAutor);
                this.obtenerAutores();
                alertify.success(`Autor ${autor.nombre} eliminado correctamente`);
            }, () => {
                //No hacer nada
            });
        },
    },
    template: `
        <div class="row justify-content-center mt-3">
            <div class="col-12">
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-body p-0">
                        <div class="p-3 bg-light border-bottom">
                            <div class="input-group shadow-sm rounded-pill overflow-hidden">
                                <span class="input-group-text bg-white border-0"><i class="bi bi-search"></i></span>
                                <input autocomplete="off" type="search" @keyup="obtenerAutores()" v-model="buscar" 
                                    placeholder="Buscar por nombre o código..." class="form-control border-0 py-2">
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" id="tblAutores">
                                <thead class="table-dark bg-gradient">
                                    <tr>
                                        <th class="ps-3">CÓDIGO</th>
                                        <th>NOMBRE</th>
                                        <th>PAÍS</th>
                                        <th>TELÉFONO</th>
                                        <th class="text-center">ACCIONES</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="autor in autores" :key="autor.idAutor" @click="modificarAutor(autor)" style="cursor: pointer;">
                                        <td class="ps-3 fw-bold text-primary">{{ autor.codigo }}</td>
                                        <td>{{ autor.nombre }}</td>
                                        <td>{{ autor.pais }}</td>
                                        <td><span class="badge bg-light text-dark border small">{{ autor.telefono }}</span></td>
                                        <td class="text-center">
                                            <button class="btn btn-outline-danger btn-sm rounded-pill px-3" @click="eliminarAutor(autor, $event)">
                                                <i class="bi bi-trash3-fill me-1"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="autores.length === 0">
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                            No se encontraron autores
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `
};
