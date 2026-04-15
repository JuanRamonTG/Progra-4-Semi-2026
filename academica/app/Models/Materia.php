<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Materia extends Model
{
    use HasFactory;

    protected $table = 'materias';
    protected $primaryKey = 'idMateria';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'idMateria', 'codigo', 'nombre', 'uv'
    ];
}
