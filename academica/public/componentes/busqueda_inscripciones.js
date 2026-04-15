const busqueda_inscripciones = {
    data(){
        return{
            buscar:'',
            inscripciones:[]
        }
    },
    methods:{
        modificarInscripcion(inscripcion){
            this.$emit('modificar', inscripcion);
        },
        async obtenerInscripciones(){
            const term = this.buscar.toLowerCase();
            this.inscripciones = await db.inscripciones.filter(
                inscripcion =>
                    inscripcion.idMatricula.toLowerCase().includes(term) ||
                    inscripcion.idMateria.toLowerCase().includes(term) ||
                    (inscripcion.idAlumno || '').toLowerCase().includes(term) ||
                    (inscripcion.ciclo || '').toLowerCase().includes(term) ||
                    (inscripcion.fecha || '').toLowerCase().includes(term)
            ).toArray();
            if( this.inscripciones.length<1 && this.buscar.length<=0){
                fetch((window.assetBase || '') + `private/modulos/inscripciones/inscripcion.php?accion=consultar`)
                    .then(response=>response.json())
                    .then(async data=>{
                        this.inscripciones = data;
                        await db.inscripciones.bulkAdd(data);
                    });
            }
        },
        
        async eliminarInscripcion(inscripcion, e){
            e.stopPropagation();
            alertify.confirm('Elimanar inscripcion', `¿Está seguro de eliminar esta inscripcion?`, async e=>{
                await db.inscripciones.delete(inscripcion.idInscripcion);
                fetch((window.assetBase || '') + `private/modulos/inscripciones/inscripcion.php?accion=eliminar&inscripciones=${JSON.stringify(inscripcion)}`)
                    .then(response=>response.json())
                    .then(data=>{
                        if(data!=true) alertify.error(`Error al sincronizar con el servidor: ${data}`);
                    });
                this.obtenerInscripciones();
                alertify.success(`Inscripcion eliminada correctamente`);
            }, () => {
                //No hacer nada
            });
        },
    },
    template: `
        <div class="row">
            <div class="col-6">
                <table class="table table-striped table-hover" id="tblInscripciones">
                    <thead>
                        <tr>
                            <th colspan="3">
                                <input autocomplete="off" type="search" @keyup="obtenerInscripciones()" v-model="buscar" placeholder="Buscar por ID" class="form-control">
                            </th>
                        </tr>
                        <tr>
                            <th>ID ALUMNO</th>
                            <th>ID MATERIA</th>
                            <th>CICLO</th>
                            <th>FECHA</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="inscripcion in inscripciones" :key="inscripcion.idInscripcion" @click="modificarInscripcion(inscripcion)">
                            <td>{{ inscripcion.idAlumno }}</td>
                            <td>{{ inscripcion.idMateria }}</td>
                            <td>{{ inscripcion.ciclo }}</td>
                            <td>{{ inscripcion.fecha }}</td>
                            <td>
                                <button class="btn btn-danger" @click="eliminarInscripcion(inscripcion, $event)">DEL</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    `
};
