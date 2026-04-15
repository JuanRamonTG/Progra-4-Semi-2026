<?php

namespace App\Http\Controllers;

use App\Models\Matricula;
use Illuminate\Http\Request;

class MatriculaController extends Controller
{
    public function index(Request $request)
    {
        $accion = $request->input('accion', '');

        if ($accion === 'consultar') {
            return response()->json(Matricula::select('idMatricula','codigo','fecha','idAlumno')->get());
        }

        $datosRaw = $request->input('matriculas');
        if (!$datosRaw) {
            return response()->json(['msg' => 'No hay datos']);
        }
        $datos = is_array($datosRaw) ? $datosRaw : json_decode($datosRaw, true);
        if (!$datos) {
            return response()->json(['msg' => 'Datos inválidos']);
        }

        if ($accion === 'nuevo' || $accion === 'modificar') {
            Matricula::updateOrCreate(
                ['idMatricula' => $datos['idMatricula']],
                [
                    'codigo'   => $datos['codigo'],
                    'fecha'    => $datos['fecha'],
                    'idAlumno' => $datos['idAlumno'],
                ]
            );
            return response()->json(['msg' => 'ok']);
        }

        if ($accion === 'eliminar') {
            Matricula::where('idMatricula', $datos['idMatricula'])->delete();
            return response()->json(true);
        }

        return response()->json(['msg' => 'Accion no reconocida']);
    }
}
