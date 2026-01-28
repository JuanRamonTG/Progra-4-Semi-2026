let idEditando = null;

document.addEventListener("DOMContentLoaded", () => {
    frmAlumnos.addEventListener("submit", (e) => {
        e.preventDefault();
       guardarAlumno();
        mostrarAlumnos();
    });
    mostrarAlumnos();
});



function mostrarAlumnos(){
    let $tblAlumnos = document.querySelector("#tblAlumnos tbody"),
        n = localStorage.length,
        filas = "";
    $tblAlumnos.innerHTML = "";
    for(let i=0; i<n; i++){
        let key = localStorage.key(i),
            data = JSON.parse(localStorage.getItem(key));
        filas += `
                <tr onclick='modificarAlumno(${JSON.stringify(data)})'>
                    <td>${data.codigo}</td>
                    <td>${data.nombre}</td>
                    <td>${data.direccion}</td>
                    <td>${data.email}</td>
                    <td>${data.telefono}</td>
                    <td>
                        <button class="btn btn-danger">DEL</button>
                    </td>
                </tr>
            `;  
    }
    $tblAlumnos.innerHTML = filas;
}
function modificarAlumno(alumno){
    idEditando = alumno.id;
    txtCodigoAlumno.value = alumno.codigo;
    txtNombreAlumno.value = alumno.nombre;
    txtDireccionAlumno.value = alumno.direccion;
    txtEmailAlumno.value = alumno.email;
    txtTelefonoAlumno.value = alumno.telefono;
}

function guardarAlumno() {
    let datos = {
        id: idEditando !== null ? idEditando : getId(),
        codigo: txtCodigoAlumno.value,
        nombre: txtNombreAlumno.value,
        direccion: txtDireccionAlumno.value,
        email: txtEmailAlumno.value,
        telefono: txtTelefonoAlumno.value
    }, codigoDuplicado = buscarAlumno(datos.codigo);
    if(codigoDuplicado && codigoDuplicado.id !== datos.id){
        alert("El codigo del alumno ya existe, "+ codigoDuplicado.nombre);
        return; //Termina la ejecucion de esta funcion
    }
    localStorage.setItem( datos.id, JSON.stringify(datos));
    limpiarFormulario();
    idEditando = null;
}

function getId(){
    return localStorage.length + 1;
}

function limpiarFormulario(){
    frmAlumnos.reset();
}

function buscarAlumno(codigo=''){
    let n = localStorage.length;
    for(let i = 0; i < n; i++){
        let datos = JSON.parse(localStorage.getItem(i));
        if(datos?.codigo && datos.codigo.trim().toUpperCase() == codigo.trim().toUpperCase()){
            return datos;
        }
    }
    return null;
}