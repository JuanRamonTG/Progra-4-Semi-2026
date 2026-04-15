<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matricula extends Model
{
    use HasFactory;

    protected $table = 'matriculas';
    protected $primaryKey = 'idMatricula';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'idMatricula', 'codigo', 'fecha', 'idAlumno'
    ];
}
