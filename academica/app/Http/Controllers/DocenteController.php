<?php

namespace App\Http\Controllers;

use App\Models\Docente;
use Illuminate\Http\Request;

class DocenteController extends Controller
{
    public function index(Request $request)
    {
        $accion = $request->input('accion', '');

        if ($accion === 'consultar') {
            return response()->json(Docente::select('idDocente','codigo','nombre','direccion','email','telefono','escalafon')->get());
        }

        $datosRaw = $request->input('docentes');
        if (!$datosRaw) {
            return response()->json(['msg' => 'No hay datos']);
        }
        $datos = is_array($datosRaw) ? $datosRaw : json_decode($datosRaw, true);
        if (!$datos) {
            return response()->json(['msg' => 'Datos inválidos']);
        }

        if ($accion === 'nuevo' || $accion === 'modificar') {
            Docente::updateOrCreate(
                ['idDocente' => $datos['idDocente']],
                [
                    'codigo'    => $datos['codigo'],
                    'nombre'    => $datos['nombre'],
                    'direccion' => $datos['direccion'],
                    'email'     => $datos['email'],
                    'telefono'  => $datos['telefono'],
                    'escalafon' => $datos['escalafon'],
                ]
            );
            return response()->json(['msg' => 'ok']);
        }

        if ($accion === 'eliminar') {
            Docente::where('idDocente', $datos['idDocente'])->delete();
            return response()->json(true);
        }

        return response()->json(['msg' => 'Accion no reconocida']);
    }
}
