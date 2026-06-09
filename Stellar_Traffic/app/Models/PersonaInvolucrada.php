<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersonaInvolucrada extends Model
{
    protected $table = 'personas_involucradas';

    protected $primaryKey = 'id_persona';

    public $timestamps = false;

    protected $fillable = [
        'id_accidente',
        'nombre_completo',
        'estado_persona',
        'observaciones',
    ];

    public function accidente()
    {
        return $this->belongsTo(Accidente::class, 'id_accidente');
    }
}
