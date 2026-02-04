const { createApp } = Vue;

createApp({
    data(){
        return{
            alumno:{
                codigo:"",
                nombre:"",
                direccion:"",
                municipio:"",
                departamento:"",
                telefono:"",
                fechaNacimiento:"",
                sexo:"",
                email:""
            },
            accion:'nuevo',
            id:0,
            buscar:'',
            alumnos:[]
        }
    },
    methods:{
        obtenerAlumnos(){
            let n = localStorage.length;
            this.alumnos = [];
            for(let i=0; i<n; i++){
                let key = localStorage.key(i);
                if( Number(key) ){
                    let data = JSON.parse(localStorage.getItem(key));
                    if( data.nombre.toUpperCase().includes(this.buscar.toUpperCase()) || 
                        data.codigo.toUpperCase().includes(this.buscar.toUpperCase()) ){
                        this.alumnos.push(data);
                    }
                }
            }
        },
        eliminarAlumno(id, e){
            e.stopPropagation();
            if(confirm("¿Está seguro de eliminar el alumno?")){
                localStorage.removeItem(id);
                this.obtenerAlumnos();
            }
        },
        modificarAlumno(alumno){
            this.accion = 'modificar';
            this.id = alumno.id;
            this.alumno.codigo = alumno.codigo;
            this.alumno.nombre = alumno.nombre;
            this.alumno.direccion = alumno.direccion;
            this.alumno.municipio = alumno.municipio;
            this.alumno.departamento = alumno.departamento;
            this.alumno.telefono = alumno.telefono;
            this.alumno.fechaNacimiento = alumno.fechaNacimiento;
            this.alumno.sexo = alumno.sexo;
            this.alumno.email = alumno.email;
        },
        guardarAlumno() {
            let datos = {
                id: this.accion=='modificar' ? this.id : this.getId(),
                codigo: this.alumno.codigo,
                nombre: this.alumno.nombre,
                direccion: this.alumno.direccion,
                municipio: this.alumno.municipio,
                departamento: this.alumno.departamento,
                telefono: this.alumno.telefono,
                fechaNacimiento: this.alumno.fechaNacimiento,
                sexo: this.alumno.sexo,
                email: this.alumno.email
            }, codigoDuplicado = this.buscarAlumno(datos.codigo);
            if(codigoDuplicado && this.accion=='nuevo'){
                alert("El codigo del alumno ya existe, "+ codigoDuplicado.nombre);
                return; //Termina la ejecucion de la funcion
            }
            localStorage.setItem( datos.id, JSON.stringify(datos));
            this.limpiarFormulario();
            this.obtenerAlumnos();
        },
        getId(){
            return new Date().getTime();
        },
        limpiarFormulario(){
            this.accion = 'nuevo';
            this.id = 0;
            this.alumno.codigo = '';
            this.alumno.nombre = '';
            this.alumno.direccion = '';
            this.alumno.municipio = '';
            this.alumno.departamento = '';
            this.alumno.telefono = '';
            this.alumno.fechaNacimiento = '';
            this.alumno.sexo = '';
            this.alumno.email = '';
        },
        buscarAlumno(codigo=''){
            let n = localStorage.length;
            for(let i = 0; i < n; i++){
                let key = localStorage.key(i);
                let datos = JSON.parse(localStorage.getItem(key));
                if(datos?.codigo && datos.codigo.trim().toUpperCase() == codigo.trim().toUpperCase()){
                    return datos;
                }
            }
            return null;
        }
    },
    mounted(){
        this.obtenerAlumnos();
        
        // Inicializar flatpickr para el campo de fecha
        flatpickr("#fechaNacimiento", {
            dateFormat: "Y-m-d",
            maxDate: new Date(),
            locale: flatpickr.l10ns.es
        });
    }
}).mount("#app");