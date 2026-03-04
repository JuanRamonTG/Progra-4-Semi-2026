const busqueda_libros = {
    data() {
        return {
            buscar: '',
            libros: [],
            autores: {}
        }
    },
    methods: {
        modificarLibro(libro) {
            this.$emit('modificar', libro);
        },
        async obtenerLibros() {
            this.libros = await db.libros.filter(
                libro => libro.titulo.toLowerCase().includes(this.buscar.toLowerCase())
                    || libro.isbn.toLowerCase().includes(this.buscar.toLowerCase())
            ).toArray();
            
            // Cargar datos de autores para mostrar nombres
            await this.cargarAutores();
        },
        async cargarAutores() {
            const todosLosAutores = await db.autores.toArray();
            this.autores = {};
            todosLosAutores.forEach(autor => {
                this.autores[autor.idAutor] = autor.nombre;
            });
        },
        obtenerNombreAutor(idAutor) {
            return this.autores[idAutor] || 'Desconocido';
        },
        async eliminarLibro(libro, e) {
            e.stopPropagation();
            alertify.confirm('Eliminar libro', `¿Está seguro de eliminar el libro "${libro.titulo}"?`, async e => {
                await db.libros.delete(libro.idLibro);
                this.obtenerLibros();
                alertify.success(`Libro "${libro.titulo}" eliminado correctamente`);
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
                                <input autocomplete="off" type="search" @keyup="obtenerLibros()" v-model="buscar" 
                                    placeholder="Buscar por título o ISBN..." class="form-control border-0 py-2">
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" id="tblLibros">
                                <thead class="table-dark bg-gradient">
                                    <tr>
                                        <th class="ps-3">ISBN</th>
                                        <th>TÍTULO</th>
                                        <th>AUTOR</th>
                                        <th>EDICIÓN</th>
                                        <th>EDITORIAL</th>
                                        <th class="text-center">ACCIONES</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="libro in libros" :key="libro.idLibro" @click="modificarLibro(libro)" style="cursor: pointer;">
                                        <td class="ps-3 fw-bold text-primary"><small class="text-muted font-monospace">{{ libro.isbn }}</small></td>
                                        <td>{{ libro.titulo }}</td>
                                        <td><span class="badge bg-info text-dark">{{ obtenerNombreAutor(libro.idAutor) }}</span></td>
                                        <td>{{ libro.edicion }}</td>
                                        <td>{{ libro.editorial }}</td>
                                        <td class="text-center">
                                            <button class="btn btn-outline-danger btn-sm rounded-pill px-3" @click="eliminarLibro(libro, $event)">
                                                <i class="bi bi-trash3-fill me-1"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="libros.length === 0">
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                            No se encontraron libros
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
