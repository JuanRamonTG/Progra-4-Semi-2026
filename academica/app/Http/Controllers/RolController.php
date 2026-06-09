<?php

namespace App\Http\Controllers;

use App\Models\Rol;

class RolController extends Controller
{
    public function index()
    {
        $roles = Rol::all();

        return view('roles.index', compact('roles'));
    }
}
