const { createApp } = Vue,
    sha256 = CryptoJS.SHA256;

async function initApp() {
    // Espera a que el módulo db.js defina "window.db".
    while (typeof window.db === 'undefined') {
        await new Promise((resolve) => setTimeout(resolve, 20));
    }

    try {
        await window.db.waitForReady();
        // Pequeño delay adicional para asegurar que todo esté completamente estable
        await new Promise((resolve) => setTimeout(resolve, 100));
    } catch (e) {
        console.error('No se pudo inicializar la base de datos local:', e);
    }

    createApp({
        components:{
            alumnos,
            busqueda_alumnos,
            materias,
            busqueda_materias,
            docentes,
            busqueda_docentes,
            matriculas,
            busqueda_matriculas,
            inscripciones,
            busqueda_inscripciones
        },
        data(){
            return{
                forms:{
                    alumnos:{mostrar:false},
                    busqueda_alumnos:{mostrar:false},
                    materias:{mostrar:false},
                    busqueda_materias:{mostrar:false},
                    docentes:{mostrar:false},
                    busqueda_docentes:{mostrar:false},
                    matriculas:{mostrar:false},
                    busqueda_matriculas:{mostrar:false},
                    inscripciones:{mostrar:false},
                    busqueda_inscripciones:{mostrar:false}
                }
            }
        },
        methods:{
            buscar(ventana, metodo){
                this.$refs[ventana][metodo]();
            },
            abrirVentana(ventana){
                this.forms[ventana].mostrar = !this.forms[ventana].mostrar;
            },
            modificar(ventana, metodo, data){
                this.$refs[ventana][metodo](data);
            }
        },
            // db schema is now handled by db.js SQLite wrapper
    }).mount("#app");
}

initApp();
