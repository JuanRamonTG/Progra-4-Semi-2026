<?php

namespace App\Http\Controllers;

use App\Models\Inscripcion;
use Illuminate\Http\Request;

class InscripcionController extends Controller
{
    public function index(Request $request)
    {
        $accion = $request->input('accion', '');

        if ($accion === 'consultar') {
            return response()->json(Inscripcion::select('idInscripcion','idMatricula','idAlumno','idMateria','ciclo','fecha')->get());
        }

        $datosRaw = $request->input('inscripciones');
        if (!$datosRaw) {
            return response()->json(['msg' => 'No hay datos']);
        }
        $datos = is_array($datosRaw) ? $datosRaw : json_decode($datosRaw, true);
        if (!$datos) {
            return response()->json(['msg' => 'Datos inválidos']);
        }

        if ($accion === 'nuevo' || $accion === 'modificar') {
            Inscripcion::updateOrCreate(
                ['idInscripcion' => $datos['idInscripcion']],
                [
                    'idMatricula' => $datos['idMatricula'],
                    'idAlumno'    => $datos['idAlumno'],
                    'idMateria'   => $datos['idMateria'],
                    'ciclo'       => $datos['ciclo'],
                    'fecha'       => $datos['fecha'],
                ]
            );
            return response()->json(['msg' => 'ok']);
        }

        if ($accion === 'eliminar') {
            Inscripcion::where('idInscripcion', $datos['idInscripcion'])->delete();
            return response()->json(true);
        }

        return response()->json(['msg' => 'Accion no reconocida']);
    }
}
