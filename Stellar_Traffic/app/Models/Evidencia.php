<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evidencia extends Model
{
    protected $table = 'evidencias';

    protected $primaryKey = 'id_evidencia';

    public $timestamps = false;

    protected $fillable = [
        'id_accidente',
        'url_archivo',
        'tipo_evidencia',
    ];

    public function accidente()
    {
        return $this->belongsTo(Accidente::class, 'id_accidente');
    }
}
