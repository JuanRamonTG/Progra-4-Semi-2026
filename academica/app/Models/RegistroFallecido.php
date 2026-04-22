<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistroFallecido extends Model
{
    use HasFactory;

    protected $table = 'registrofallecidos';

    protected $fillable = [
        'fecha_hora',
        'ubicacion',
        'descripcion',
        'verificacion',
        'fotos',
        'testigos',
        'hora_fallecimiento',
        'estado',
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
        'hora_fallecimiento' => 'datetime',
        'verificacion' => 'boolean',
    ];
}
