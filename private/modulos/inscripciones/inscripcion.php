<?php
include('../../Config/Config.php');

// Leer JSON del body si viene como application/json
$input = file_get_contents('php://input');
$json = json_decode($input, true);

if ($json && is_array($json)) {
    $inscripciones = isset($json['inscripciones']) ? json_encode($json['inscripciones']) : '[]';
    $accion        = $json['accion'] ?? '';
} else {
    extract($_REQUEST);
    $inscripciones = $inscripciones ?? '[]';
    $accion        = $accion        ?? '';
}

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
        if(!isset($this->datos['idMatricula']) || trim((string)$this->datos['idMatricula']) === ''){
            $this->respuesta['msg'] = 'El id de la matricula es requerido';
            return $this->respuesta;
        }
        if(!isset($this->datos['idMateria']) || trim((string)$this->datos['idMateria']) === ''){
            $this->respuesta['msg'] = 'El id de la materia es requerido';
            return $this->respuesta;
        }
        if(!isset($this->datos['ciclo']) || trim((string)$this->datos['ciclo']) === ''){
            $this->respuesta['msg'] = 'El ciclo es requerido';
            return $this->respuesta;
        }
        if(!isset($this->datos['fecha']) || trim($this->datos['fecha']) === ''){
            $this->respuesta['msg'] = 'La fecha es requerida';
            return $this->respuesta;
        }
        if(!isset($this->datos['idAlumno']) || trim((string)$this->datos['idAlumno']) === ''){
            $this->respuesta['msg'] = 'El id del alumno es requerido';
            return $this->respuesta;
        }
        return $this->administrar_inscripciones();
    }
    private function administrar_inscripciones(){
        global $accion;
        if($this->respuesta['msg']!=='ok'){
           return $this->respuesta;
        }
        if($accion==='nuevo'){
            $this->db->consultaSQL('SELECT idInscripcion FROM inscripciones WHERE idInscripcion = ?', $this->datos['idInscripcion']);
            $existente = $this->db->obtener_datos();
            if(!empty($existente)){
                return $this->db->consultaSQL('UPDATE inscripciones SET idMatricula = ?, idMateria = ?, idAlumno = ?, ciclo = ?, fecha = ? WHERE idInscripcion = ?',
                    $this->datos['idMatricula'], $this->datos['idMateria'], $this->datos['idAlumno'], $this->datos['ciclo'], $this->datos['fecha'], $this->datos['idInscripcion']);
            }
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