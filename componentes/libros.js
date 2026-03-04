const libros = {
    props: ['forms'],
    data() {
        return {
            libro: {
                idLibro: 0,
                titulo: "",
                idAutor: null,
                edicion: "",
                editorial: "",
                isbn: ""
            },
            accion: 'nuevo',
            idLibro: 0,
            autoresDisponibles: [],
            autorSeleccionado: null,
            data_libros: []
        }
    },
    methods: {
        buscarLibro() {
            this.forms.busqueda_libros.mostrar = !this.forms.busqueda_libros.mostrar;
            this.$emit('buscar');
        },
        modificarLibro(libro) {
            this.accion = 'modificar';
            this.idLibro = libro.idLibro;
            this.libro.titulo = libro.titulo;
            this.libro.idAutor = libro.idAutor;
            this.libro.edicion = libro.edicion;
            this.libro.editorial = libro.editorial;
            this.libro.isbn = libro.isbn;
            
            // Cargar el autor seleccionado
            this.autorSeleccionado = this.autoresDisponibles.find(a => a.idAutor === libro.idAutor);
        },
        async cargarAutores() {
            this.autoresDisponibles = await db.autores.toArray();
        },
        async guardarLibro() {
            try {
                if (!this.autorSeleccionado) {
                    alertify.error("Debe seleccionar un autor");
                    return;
                }

                // Validar duplicidad de ISBN
                const librosExistentes = await db.libros.where('isbn').equals(this.libro.isbn).toArray();
                if (librosExistentes.length > 0 && this.accion == 'nuevo') {
                    alertify.error(`El ISBN ${this.libro.isbn} ya existe`);
                    return;
                }

                let datos = {
                    idLibro: this.accion == 'modificar' ? this.idLibro : this.getId(),
                    titulo: this.libro.titulo,
                    idAutor: this.autorSeleccionado.idAutor,
                    edicion: this.libro.edicion,
                    editorial: this.libro.editorial,
                    isbn: this.libro.isbn
                };

                await db.libros.put(datos);
                this.limpiarFormulario();
                alertify.success(`Libro "${datos.titulo}" guardado correctamente`);
                this.$emit('guardar');
            } catch (error) {
                console.error(error);
                alertify.error("Error al guardar libro");
            }
        },
        getId() {
            return new Date().getTime();
        },
        limpiarFormulario() {
            this.accion = 'nuevo';
            this.idLibro = 0;
            this.libro.titulo = '';
            this.libro.idAutor = null;
            this.libro.edicion = '';
            this.libro.editorial = '';
            this.libro.isbn = '';
            this.autorSeleccionado = null;
        },
    },
    mounted() {
        this.cargarAutores();
    },
    template: `
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <form id="frmLibros" @submit.prevent="guardarLibro" @reset.prevent="limpiarFormulario">
                    <div class="card shadow border-0 rounded-3 overflow-hidden">
                        <div class="card-header bg-dark bg-gradient text-white py-3">
                            <h5 class="card-title mb-0 fw-bold">
                                <i class="bi bi-book me-2"></i>REGISTRO DE LIBROS
                            </h5>
                        </div>
                        <div class="card-body p-4 bg-white">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold small text-secondary">TÍTULO</label>
                                    <input placeholder="Ej: Don Quijote" required v-model="libro.titulo" type="text" class="form-control bg-light border-0 py-2">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold small text-secondary">AUTOR</label>
                                    <v-select 
                                        v-model="autorSeleccionado"
                                        :options="autoresDisponibles" 
                                        label="nombre"
                                        :reduce="autor => autor"
                                        placeholder="Seleccione un autor..."
                                        class="form-control p-0 border-0 bg-light">
                                        <template #option="option">
                                            <div class="d-flex justify-content-between w-100">
                                                <strong>{{ option.nombre }}</strong>
                                                <small class="text-muted">{{ option.codigo }}</small>
                                            </div>
                                        </template>
                                        <template #selected-option="option">
                                            <div v-if="option" class="selected d-flex justify-content-between w-100">
                                                <span><strong>{{ option.nombre }}</strong></span>
                                                <small class="text-muted">{{ option.codigo }}</small>
                                            </div>
                                        </template>
                                    </v-select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small text-secondary">EDICIÓN</label>
                                    <input placeholder="Ej: 1ª" required v-model="libro.edicion" type="text" class="form-control bg-light border-0 py-2">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small text-secondary">EDITORIAL</label>
                                    <input placeholder="Ej: Planeta" required v-model="libro.editorial" type="text" class="form-control bg-light border-0 py-2">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small text-secondary">ISBN</label>
                                    <input placeholder="Ej: 978-1234567890" required v-model="libro.isbn" type="text" class="form-control bg-light border-0 py-2">
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-light p-3 d-flex gap-2 justify-content-end">
                            <button type="reset" class="btn btn-outline-secondary rounded-pill px-4">
                                <i class="bi bi-arrow-counterclockwise me-2"></i>Limpiar
                            </button>
                            <button type="button" class="btn btn-outline-primary rounded-pill px-4" @click="buscarLibro()">
                                <i class="bi bi-search me-2"></i>Consultar
                            </button>
                            <button type="submit" class="btn btn-primary rounded-pill px-4">
                                <i class="bi bi-check-circle me-2"></i>Guardar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    `
};
