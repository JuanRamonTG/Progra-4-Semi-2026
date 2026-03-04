const autores = {
    props: ['forms'],
    data() {
        return {
            autor: {
                idAutor: 0,
                codigo: "",
                nombre: "",
                pais: "",
                telefono: ""
            },
            accion: 'nuevo',
            idAutor: 0,
            data_autores: []
        }
    },
    methods: {
        buscarAutor() {
            this.forms.busqueda_autores.mostrar = !this.forms.busqueda_autores.mostrar;
            this.$emit('buscar');
        },
        modificarAutor(autor) {
            this.accion = 'modificar';
            this.idAutor = autor.idAutor;
            this.autor.codigo = autor.codigo;
            this.autor.nombre = autor.nombre;
            this.autor.pais = autor.pais;
            this.autor.telefono = autor.telefono;
        },
        async guardarAutor() {
            try {
                // Validar duplicidad de código
                const autoresExistentes = await db.autores.where('codigo').equals(this.autor.codigo).toArray();
                if (autoresExistentes.length > 0 && this.accion == 'nuevo') {
                    alertify.error(`El código ${this.autor.codigo} ya pertenece a ${autoresExistentes[0].nombre}`);
                    return;
                }

                let datos = {
                    idAutor: this.accion == 'modificar' ? this.idAutor : this.getId(),
                    codigo: this.autor.codigo,
                    nombre: this.autor.nombre,
                    pais: this.autor.pais,
                    telefono: this.autor.telefono
                };

                await db.autores.put(datos);
                this.limpiarFormulario();
                alertify.success(`${datos.nombre} guardado correctamente`);
                this.$emit('guardar');
            } catch (error) {
                console.error(error);
                alertify.error("Error al guardar autor");
            }
        },
        getId() {
            return new Date().getTime();
        },
        limpiarFormulario() {
            this.accion = 'nuevo';
            this.idAutor = 0;
            this.autor.codigo = '';
            this.autor.nombre = '';
            this.autor.pais = '';
            this.autor.telefono = '';
        },
    },
    template: `
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <form id="frmAutores" @submit.prevent="guardarAutor" @reset.prevent="limpiarFormulario">
                    <div class="card shadow border-0 rounded-3 overflow-hidden">
                        <div class="card-header bg-dark bg-gradient text-white py-3">
                            <h5 class="card-title mb-0 fw-bold">
                                <i class="bi bi-pen-fill me-2"></i>REGISTRO DE AUTORES
                            </h5>
                        </div>
                        <div class="card-body p-4 bg-white">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small text-secondary">CÓDIGO</label>
                                    <input placeholder="Ej: AU001" required v-model="autor.codigo" type="text" class="form-control bg-light border-0 py-2">
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label fw-semibold small text-secondary">NOMBRE</label>
                                    <input placeholder="Ej: Jorge García" required v-model="autor.nombre" type="text" class="form-control bg-light border-0 py-2">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small text-secondary">PAÍS</label>
                                    <input placeholder="Ej: Salvadoreño" required v-model="autor.pais" type="text" class="form-control bg-light border-0 py-2">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small text-secondary">TELÉFONO</label>
                                    <input placeholder="Ej: +503 1234-5678" required v-model="autor.telefono" type="tel" class="form-control bg-light border-0 py-2">
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-light p-3 d-flex gap-2 justify-content-end">
                            <button type="reset" class="btn btn-outline-secondary rounded-pill px-4">
                                <i class="bi bi-arrow-counterclockwise me-2"></i>Limpiar
                            </button>
                            <button type="button" class="btn btn-outline-primary rounded-pill px-4" @click="buscarAutor()">
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
