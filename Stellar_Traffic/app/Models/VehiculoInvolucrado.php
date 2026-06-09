<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehiculoInvolucrado extends Model
{
    protected $table = 'vehiculos_involucrados';

    protected $primaryKey = 'id_vehiculo';

    public $timestamps = false;

    protected $fillable = [
        'id_accidente',
        'marca',
        'modelo',
        'tipo_vehiculo',
        'anio',
        'propietario',
    ];

    public function accidente()
    {
        return $this->belongsTo(Accidente::class, 'id_accidente');
    }
}
