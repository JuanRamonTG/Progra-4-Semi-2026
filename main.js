const { createApp } = Vue,
    Dexie = window.Dexie,
    db = new Dexie("db_usis054021"),
    sha256 = CryptoJS.SHA256;

// Inicializar la BD ANTES de crear la app
db.version(4).stores({
    "autores": "idAutor, codigo, nombre, pais, telefono",
    "libros": "idLibro, titulo, idAutor, isbn, editorial, edicion"
});

const app = createApp({
    components: {
        autores,
        busqueda_autores,
        libros,
        busqueda_libros
    },
    data() {
        return {
            forms: {
                autores: { mostrar: true },
                busqueda_autores: { mostrar: false },
                libros: { mostrar: false },
                busqueda_libros: { mostrar: false }
            }
        }
    },
    methods: {
        buscar(ventana, metodo) {
            this.$refs[ventana][metodo]();
        },
        abrirVentana(ventana) {
            this.forms[ventana].mostrar = !this.forms[ventana].mostrar;
        },
        modificar(ventana, metodo, data) {
            this.$refs[ventana][metodo](data);
        },
        recargarAutoresEnLibros() {
            if (this.$refs.libros && this.$refs.libros.cargarAutores) {
                this.$refs.libros.cargarAutores();
            }
        },
        guardarAutor() {
            this.buscar("busqueda_autores", "obtenerAutores");
            this.recargarAutoresEnLibros();
        }
    }
});

// Registro GLOBAL del componente v-select (defensivo para v4 beta o v3)
const vSelectComponent = window.vSelect ||
    window.VueSelect?.default ||
    window.VueSelect ||
    window["vue-select"]?.default ||
    window["vue-select"];

if (vSelectComponent) {
    app.component('v-select', vSelectComponent);
} else {
    console.warn("v-select no se pudo cargar. Verifique la conexión al CDN.");
}

app.mount("#app");
