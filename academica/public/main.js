const { createApp } = Vue,
    sha256 = CryptoJS.SHA256;

async function initApp() {
    // Espera a que db.js intente inicializar SQLite WASM (con timeout de 8 seg)
    // Si falla o no está disponible, continúa sin base local
    try {
        await Promise.race([
            globalThis.dbReady,
            new Promise((_, reject) => setTimeout(() => reject(new Error('timeout')), 8000))
        ]);
        console.log('[App] SQLite WASM listo, usando modo offline+server.');
    } catch (e) {
        console.warn('[App] SQLite WASM no disponible, usando solo servidor:', e.message);
        // Crea un objeto db vacío para que los componentes no rompan
        globalThis.db = {
            waitForReady: () => Promise.resolve(),
            alumnos:       crearAdaptadorVacio(),
            materias:      crearAdaptadorVacio(),
            docentes:      crearAdaptadorVacio(),
            matriculas:    crearAdaptadorVacio(),
            inscripciones: crearAdaptadorVacio(),
        };
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
        }
    }).mount("#app");
}

// Adaptador vacío: los métodos devuelven arrays vacíos o no hacen nada
// Los componentes seguirán funcionando pero cargarán datos solo del servidor
function crearAdaptadorVacio() {
    const arrVacio = () => Promise.resolve([]);
    return {
        waitForReady: () => Promise.resolve(),
        put:      () => Promise.resolve(),
        delete:   () => Promise.resolve(),
        bulkAdd:  () => Promise.resolve(),
        filter:   ()  => ({ toArray: arrVacio }),
        orderBy:  ()  => ({ filter: () => ({ toArray: arrVacio }) }),
    };
}

initApp();
