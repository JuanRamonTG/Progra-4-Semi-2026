<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class Alumno extends Model
{
    use HasFactory;

    protected $table = 'alumnos';
    protected $primaryKey = 'idAlumno';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'idAlumno', 'codigo', 'nombre', 'direccion', 'email', 'telefono', 'hash'
    ];
}
