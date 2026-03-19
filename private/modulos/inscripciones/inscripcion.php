<?php
include('../../Config/Config.php');
extract($_REQUEST);

$inscripciones = $inscripciones ?? '[]';
$accion = $accion ?? '';

$class_inscripciones = new inscripciones($conexion);
echo json_encode($class_inscripciones->recibir_datos($inscripciones));

class inscripciones{
    private $datos = [], $db, $respuesta=['msg'=>'ok'];

    public function __construct($conexion){
        $this->db = $conexion;
    }
    public function recibir_datos($inscripciones){
        global $accion;
        if($accion==='consultar'){
            return $this->administrar_inscripciones();
        }else{
            $this->datos = json_decode($inscripciones, true);
            return $this->validar_datos();
        }
    }
    private function validar_datos(){
        if(empty($this->datos['idMatricula'])){
            $this->respuesta['msg'] = 'El id de la matricula es requerido';
        }
        if(empty($this->datos['idMateria'])){
            $this->respuesta['msg'] = 'El id de la materia es requerido';
        }
        if(empty($this->datos['ciclo'])){
            $this->respuesta['msg'] = 'El ciclo es requerido';
        }
        if(empty($this->datos['fecha'])){
            $this->respuesta['msg'] = 'La fecha es requerida';
        }
        if(empty($this->datos['idAlumno'])){
            $this->respuesta['msg'] = 'El id del alumno es requerido';
        }
        return $this->administrar_inscripciones();
    }
    private function administrar_inscripciones(){
        global $accion;
        if($this->respuesta['msg']!=='ok'){
           return $this->respuesta;
        }
        if($accion==='nuevo'){
            // Verificar si ya existe una inscripción con este idInscripcion
            $existente = $this->db->consultaSQL('SELECT idInscripcion FROM inscripciones WHERE idInscripcion = ?', $this->datos['idInscripcion']);
            if(!empty($existente)){
                // Si existe, actualizar en lugar de insertar
                return $this->db->consultaSQL('UPDATE inscripciones SET idMatricula = ?, idMateria = ?, idAlumno = ?, ciclo = ?, fecha = ? WHERE idInscripcion = ?',
                    $this->datos['idMatricula'], $this->datos['idMateria'], $this->datos['idAlumno'], $this->datos['ciclo'], $this->datos['fecha'], $this->datos['idInscripcion']);
            }
            // Si no existe, insertar normalmente
            return $this->db->consultaSQL('INSERT INTO inscripciones (idInscripcion, idMatricula, idMateria, idAlumno, ciclo, fecha) VALUES (?, ?, ?, ?, ?, ?)',
            $this->datos['idInscripcion'], $this->datos['idMatricula'], $this->datos['idMateria'], $this->datos['idAlumno'], $this->datos['ciclo'], $this->datos['fecha']);
        }else if($accion==='modificar'){
            return $this->db->consultaSQL('UPDATE inscripciones SET idMatricula = ?, idMateria = ?, idAlumno = ?, ciclo = ?, fecha = ? WHERE idInscripcion = ?',
            $this->datos['idMatricula'], $this->datos['idMateria'], $this->datos['idAlumno'], $this->datos['ciclo'], $this->datos['fecha'], $this->datos['idInscripcion']);
        }else if($accion==='eliminar'){
            return $this->db->consultaSQL('
                DELETE FROM inscripciones 
                WHERE idInscripcion = ?
            ',$this->datos['idInscripcion']);
        }else if($accion==='consultar'){
            $this->db->consultaSQL('
                SELECT idInscripcion, idMatricula, idMateria, idAlumno, ciclo, fecha 
                FROM inscripciones
            ');
            return $this->db->obtener_datos();
        }
    }
}
?>