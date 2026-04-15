<?php

namespace App\Http\Controllers;

use App\Models\Materia;
use Illuminate\Http\Request;

class MateriaController extends Controller
{
    public function index(Request $request)
    {
        $accion = $request->input('accion', '');

        if ($accion === 'consultar') {
            return response()->json(Materia::select('idMateria','codigo','nombre','uv')->get());
        }

        $datosRaw = $request->input('materias');
        if (!$datosRaw) {
            return response()->json(['msg' => 'No hay datos']);
        }
        $datos = is_array($datosRaw) ? $datosRaw : json_decode($datosRaw, true);
        if (!$datos) {
            return response()->json(['msg' => 'Datos inválidos']);
        }

        if ($accion === 'nuevo' || $accion === 'modificar') {
            Materia::updateOrCreate(
                ['idMateria' => $datos['idMateria']],
                [
                    'codigo' => $datos['codigo'],
                    'nombre' => $datos['nombre'],
                    'uv'     => $datos['uv'],
                ]
            );
            return response()->json(['msg' => 'ok']);
        }

        if ($accion === 'eliminar') {
            Materia::where('idMateria', $datos['idMateria'])->delete();
            return response()->json(true);
        }

        return response()->json(['msg' => 'Accion no reconocida']);
    }
}
