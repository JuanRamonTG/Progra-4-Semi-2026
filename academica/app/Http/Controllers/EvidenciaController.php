<?php

namespace App\Http\Controllers;

use App\Models\Evidencia;

class EvidenciaController extends Controller
{
    public function index()
    {
        $evidencias = Evidencia::all();

        return view('evidencias.index', compact('evidencias'));
    }
}
