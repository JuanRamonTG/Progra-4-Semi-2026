<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inscripcion extends Model
{
    use HasFactory;

    protected $table = 'inscripciones';
    protected $primaryKey = 'idInscripcion';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'idInscripcion', 'idMatricula', 'idAlumno', 'idMateria', 'ciclo', 'fecha'
    ];
}
