<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Docente extends Model
{
    use HasFactory;

    protected $table = 'docentes';
    protected $primaryKey = 'idDocente';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'idDocente', 'codigo', 'nombre', 'direccion', 'email', 'telefono', 'escalafon'
    ];
}
