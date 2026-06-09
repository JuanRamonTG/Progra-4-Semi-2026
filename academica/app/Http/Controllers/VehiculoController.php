<?php

namespace App\Http\Controllers;

use App\Models\VehiculoInvolucrado;

class VehiculoController extends Controller
{
    public function index()
    {
        $vehiculos = VehiculoInvolucrado::all();

        return view('vehiculos.index', compact('vehiculos'));
    }
}
