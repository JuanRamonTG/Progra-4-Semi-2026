const busqueda_matriculas = {
    data(){
        return{
            buscar:'',
            matriculas:[]
        }
    },
    methods:{
        modificarMatricula(matricula){
            this.$emit('modificar', matricula);
        },
        async obtenerMatriculas(){
            const term = this.buscar.toLowerCase();
            this.matriculas = await db.matriculas.filter(
                matricula =>
                    matricula.codigo.toLowerCase().includes(term) ||
                    (matricula.idAlumno || '').toLowerCase().includes(term)
            ).toArray();
            if( this.matriculas.length<1 && this.buscar.length<=0){
                fetch(`private/modulos/matriculas/matricula.php?accion=consultar`)
                    .then(response=>response.json())
                    .then(async data=>{
                        this.matriculas = data;
                        await db.matriculas.bulkAdd(data);
                    });
            }
        },
        
        async eliminarMatricula(matricula, e){
            e.stopPropagation();
            alertify.confirm('Elimanar matriculas', `¿Está seguro de eliminar la matricula ${matricula.codigo}?`, async e=>{
                await db.matriculas.delete(matricula.idMatricula);
                fetch(`private/modulos/matriculas/matricula.php?accion=eliminar&matriculas=${JSON.stringify(matricula)}`)
                    .then(response=>response.json())
                    .then(data=>{
                        if(data!=true) alertify.error(`Error al sincronizar con el servidor: ${data}`);
                    });
                this.obtenerMatriculas();
                alertify.success(`Matricula ${matricula.codigo} eliminada correctamente`);
            }, () => {
                //No hacer nada
            });
        },
    },
    template: `
        <div class="row">
            <div class="col-6">
                <table class="table table-striped table-hover" id="tblMatriculas">
                    <thead>
                        <tr>
                            <th colspan="4">
                                <input autocomplete="off" type="search" @keyup="obtenerMatriculas()" v-model="buscar" placeholder="Buscar matricula" class="form-control">
                            </th>
                        </tr>
                        <tr>
                            <th>CICLO</th>
                            <th>FECHA</th>
                            <th>ID ALUMNO</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="matricula in matriculas" :key="matricula.idMatricula" @click="modificarMatricula(matricula)">
                            <td>{{ matricula.codigo }}</td>
                            <td>{{ matricula.fecha }}</td>
                            <td>{{ matricula.idAlumno }}</td>
                            <td>
                                <button class="btn btn-danger" @click="eliminarMatricula(matricula, $event)">DEL</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    `
};
