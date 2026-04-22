<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistroMaterial extends Model
{
    use HasFactory;

    protected $table = 'registromateriales';

    protected $fillable = [
        'fecha_hora',
        'ubicacion',
        'descripcion',
        'verificacion',
        'fotos',
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
        'verificacion' => 'boolean',
    ];
}
