<?php

namespace App\Http\Controllers;

use App\Models\PersonaInvolucrada;

class PersonaController extends Controller
{
    public function index()
    {
        $personas = PersonaInvolucrada::all();

        return view('personas.index', compact('personas'));
    }
}
