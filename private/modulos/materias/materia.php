<?php
include('../../Config/Config.php');

// Leer JSON del body si viene como application/json
$input = file_get_contents('php://input');
$json = json_decode($input, true);

if ($json && is_array($json)) {
    $materias = isset($json['materias']) ? json_encode($json['materias']) : '[]';
    $accion   = $json['accion'] ?? '';
} else {
    extract($_REQUEST);
    $materias = $materias ?? '[]';
    $accion   = $accion   ?? '';
}

$class_materias = new materias($conexion);
echo json_encode($class_materias->recibir_datos($materias));

class materias{
    private $datos = [], $db, $respuesta=['msg'=>'ok'];

    public function __construct($conexion){
        $this->db = $conexion;
    }
    public function recibir_datos($materias){
        global $accion;
        if($accion==='consultar'){
            return $this->administrar_materias();
        }else{
            $this->datos = json_decode($materias, true);
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
        if(!isset($this->datos['uv']) || trim((string)$this->datos['uv']) === ''){
            $this->respuesta['msg'] = 'La cantidad de UV es requerida';
            return $this->respuesta;
        }
        return $this->administrar_materias();
    }
    private function administrar_materias(){
        global $accion;
        if($this->respuesta['msg']!=='ok'){
           return $this->respuesta;
        }
        if($accion==='nuevo'){
            // consultaSQL() retorna true (PDO execute), hay que leer filas con obtener_datos()
            $this->db->consultaSQL('SELECT idMateria FROM materias WHERE idMateria = ?', $this->datos['idMateria']);
            $existente = $this->db->obtener_datos();
            if(!empty($existente)){
                // Ya existe: actualizar
                return $this->db->consultaSQL('UPDATE materias SET codigo = ?, nombre = ?, uv = ? WHERE idMateria = ?',
                    $this->datos['codigo'], $this->datos['nombre'], $this->datos['uv'], $this->datos['idMateria']);
            }
            // No existe: insertar
            return $this->db->consultaSQL('INSERT INTO materias (idMateria, codigo, nombre, uv) VALUES (?, ?, ?, ?)',
            $this->datos['idMateria'], $this->datos['codigo'], $this->datos['nombre'], $this->datos['uv']);
        }else if($accion==='modificar'){
            return $this->db->consultaSQL('UPDATE materias SET codigo = ?, nombre = ?, uv = ? WHERE idMateria = ?',
            $this->datos['codigo'], $this->datos['nombre'], $this->datos['uv'], $this->datos['idMateria']);
        }else if($accion==='eliminar'){
            return $this->db->consultaSQL('
                DELETE FROM materias 
                WHERE idMateria = ?
            ',$this->datos['idMateria']);
        }else if($accion==='consultar'){
            $this->db->consultaSQL('
                SELECT idMateria, codigo, nombre, uv 
                FROM materias
            ');
            return $this->db->obtener_datos();
        }
    }
}
