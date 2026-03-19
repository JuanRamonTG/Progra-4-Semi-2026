<?php
include('../../Config/Config.php');

// Leer JSON del body si viene como application/json
$input = file_get_contents('php://input');
$json = json_decode($input, true);

if ($json && is_array($json)) {
    $docentes = isset($json['docentes']) ? json_encode($json['docentes']) : '[]';
    $accion   = $json['accion'] ?? '';
} else {
    extract($_REQUEST);
    $docentes = $docentes ?? '[]';
    $accion   = $accion   ?? '';
}

$class_docentes = new docentes($conexion);
echo json_encode($class_docentes->recibir_datos($docentes));

class docentes{
    private $datos = [], $db, $respuesta=['msg'=>'ok'];

    public function __construct($conexion){
        $this->db = $conexion;
    }
    public function recibir_datos($docentes){
        global $accion;
        if($accion==='consultar'){
            return $this->administrar_docentes();
        }else{
            $this->datos = json_decode($docentes, true);
            return $this->validar_datos();
        }
    }
    private function validar_datos(){
        if(!isset($this->datos['codigo']) || trim($this->datos['codigo']) === ''){
            $this->respuesta['msg'] = 'El codigo es requerido';
            return $this->respuesta;
        }
        if(!isset($this->datos['nombre']) || trim($this->datos['nombre']) === ''){
            $this->respuesta['msg'] = 'El nombre es requerido';
            return $this->respuesta;
        }
        if(!isset($this->datos['direccion']) || trim($this->datos['direccion']) === ''){
            $this->respuesta['msg'] = 'La direccion es requerida';
            return $this->respuesta;
        }
        if(!isset($this->datos['email']) || trim($this->datos['email']) === ''){
            $this->respuesta['msg'] = 'El email es requerido';
            return $this->respuesta;
        }
        if(!isset($this->datos['telefono']) || trim($this->datos['telefono']) === ''){
            $this->respuesta['msg'] = 'El telefono es requerido';
            return $this->respuesta;
        }
        if(!isset($this->datos['escalafon']) || trim($this->datos['escalafon']) === ''){
            $this->respuesta['msg'] = 'El escalafon es requerido';
            return $this->respuesta;
        }
        return $this->administrar_docentes();
    }
    private function administrar_docentes(){
        global $accion;
        if($this->respuesta['msg']!=='ok'){
           return $this->respuesta;
        }
        if($accion==='nuevo'){
            $this->db->consultaSQL('SELECT idDocente FROM docentes WHERE idDocente = ?', $this->datos['idDocente']);
            $existente = $this->db->obtener_datos();
            if(!empty($existente)){
                return $this->db->consultaSQL('UPDATE docentes SET codigo = ?, nombre = ?, direccion = ?, email = ?, telefono = ?, escalafon = ? WHERE idDocente = ?',
                    $this->datos['codigo'], $this->datos['nombre'], $this->datos['direccion'], $this->datos['email'], $this->datos['telefono'], $this->datos['escalafon'], $this->datos['idDocente']);
            }
            return $this->db->consultaSQL('INSERT INTO docentes (idDocente, codigo, nombre, direccion, email, telefono, escalafon) VALUES (?, ?, ?, ?, ?, ?, ?)',
            $this->datos['idDocente'], $this->datos['codigo'], $this->datos['nombre'], $this->datos['direccion'], $this->datos['email'], $this->datos['telefono'], $this->datos['escalafon']);
        }else if($accion==='modificar'){
            return $this->db->consultaSQL('UPDATE docentes SET codigo = ?, nombre = ?, direccion = ?, email = ?, telefono = ?, escalafon = ? WHERE idDocente = ?',
            $this->datos['codigo'], $this->datos['nombre'], $this->datos['direccion'], $this->datos['email'], $this->datos['telefono'], $this->datos['escalafon'], $this->datos['idDocente']);
        }else if($accion==='eliminar'){
            return $this->db->consultaSQL('
                DELETE FROM docentes 
                WHERE idDocente = ?
            ',$this->datos['idDocente']);
        }else if($accion==='consultar'){
            $this->db->consultaSQL('
                SELECT idDocente, codigo, nombre, direccion, email, telefono, escalafon 
                FROM docentes
            ');
            return $this->db->obtener_datos();
        }
    }
}
?>