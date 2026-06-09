<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Accidente extends Model
{
    // La PK real de la tabla es id_accidente
    protected $primaryKey = 'id_accidente';

    // La tabla no tiene created_at / updated_at (usa fecha_registro)
    public $timestamps = false;

    protected $fillable = [
        'id_caso',
        'tipo_accidente',
        'fecha_incidente',
        'hora_aproximada',
        'gravedad',
        'direccion',
        'municipio',
        'descripcion',
        'condicion_climatica',
        'tipo_via',
        'estado_pavimento',
        'declaracion_involucrados',
        'id_usuario',
    ];
}
