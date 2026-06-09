<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Accidente extends Model
{
    // Nombre de la tabla en la base de datos
    protected $table = 'accidentes';

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

    // Relación con el usuario que registró el accidente
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    // Relación con las evidencias asociadas
    public function evidencias()
    {
        return $this->hasMany(Evidencia::class, 'id_accidente');
    }

    // Relación con las personas involucradas en el accidente
    public function personasInvolucradas()
    {
        return $this->hasMany(PersonaInvolucrada::class, 'id_accidente');
    }

    // Relación con los vehículos involucrados en el accidente
    public function vehiculosInvolucrados()
    {
        return $this->hasMany(VehiculoInvolucrado::class, 'id_accidente');
    }
}
