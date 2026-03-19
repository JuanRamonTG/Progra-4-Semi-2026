<?php
include('../../Config/Config.php');
extract($_REQUEST);

$matriculas = $matriculas ?? '[]';
$accion = $accion ?? '';

$class_matriculas = new matriculas($conexion);
echo json_encode($class_matriculas->recibir_datos($matriculas));

class matriculas{
    private $datos = [], $db, $respuesta=['msg'=>'ok'];

    public function __construct($conexion){
        $this->db = $conexion;
    }
    public function recibir_datos($matriculas){
        global $accion;
        if($accion==='consultar'){
            return $this->administrar_matriculas();
        }else{
            $this->datos = json_decode($matriculas, true);
            return $this->validar_datos();
        }
    }
    private function validar_datos(){
        if(empty($this->datos['codigo'])){
            $this->respuesta['msg'] = 'El codigo es requerido';
        }
        if(empty($this->datos['fecha'])){
            $this->respuesta['msg'] = 'La fecha es requerida';
        }
        if(empty($this->datos['idAlumno'])){
            $this->respuesta['msg'] = 'El id del alumno es requerido';
        }
        return $this->administrar_matriculas();
    }
    private function administrar_matriculas(){
        global $accion;
        if($this->respuesta['msg']!=='ok'){
           return $this->respuesta;
        }
        if($accion==='nuevo'){
            return $this->db->consultaSQL('INSERT INTO matriculas (idMatricula, codigo, fecha, idAlumno) VALUES (?, ?, ?, ?)',
            $this->datos['idMatricula'], $this->datos['codigo'], $this->datos['fecha'], $this->datos['idAlumno']);
        }else if($accion==='modificar'){
            return $this->db->consultaSQL('UPDATE matriculas SET codigo = ?, fecha = ?, idAlumno = ? WHERE idMatricula = ?',
            $this->datos['codigo'], $this->datos['fecha'], $this->datos['idAlumno'], $this->datos['idMatricula']);
        }else if($accion==='eliminar'){
            return $this->db->consultaSQL('
                DELETE FROM matriculas 
                WHERE idMatricula = ?
            ',$this->datos['idMatricula']);
        }else if($accion==='consultar'){
            $this->db->consultaSQL('
                SELECT idMatricula, codigo, fecha, idAlumno 
                FROM matriculas
            ');
            return $this->db->obtener_datos();
        }
    }
}
?>