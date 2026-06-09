<?php

namespace App\Http\Controllers;

use App\Models\User; // O Usuario, según tu archivo

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = User::all();

        return view('usuarios.index', compact('usuarios'));
    }
}
