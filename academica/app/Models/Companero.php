<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Companero extends Model
{
    protected $fillable = ['nombre'];
    protected $table = 'companeros';
}
