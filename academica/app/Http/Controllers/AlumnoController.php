<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use Illuminate\Http\Request;

class AlumnoController extends Controller
{
    public function index(Request $request)
    {
        $accion = $request->input('accion', '');

        if ($accion === 'consultar') {
            return response()->json(Alumno::select('idAlumno','codigo','nombre','direccion','email','telefono','hash')->get());
        }

        // Lee datos: puede venir como JSON body (POST) o como query string JSON (GET)
        $datosRaw = $request->input('alumnos');
        if (!$datosRaw) {
            return response()->json(['msg' => 'No hay datos']);
        }
        $datos = is_array($datosRaw) ? $datosRaw : json_decode($datosRaw, true);
        if (!$datos) {
            return response()->json(['msg' => 'Datos inválidos']);
        }

        // Validaciones básicas
        foreach (['codigo', 'nombre', 'direccion', 'email', 'telefono'] as $campo) {
            if (empty(trim($datos[$campo] ?? ''))) {
                return response()->json(['msg' => "El campo $campo es requerido"]);
            }
        }

        if ($accion === 'nuevo' || $accion === 'modificar') {
            Alumno::updateOrCreate(
                ['idAlumno' => $datos['idAlumno']],
                [
                    'codigo'    => $datos['codigo'],
                    'nombre'    => $datos['nombre'],
                    'direccion' => $datos['direccion'],
                    'email'     => $datos['email'],
                    'telefono'  => $datos['telefono'],
                    'hash'      => $datos['hash'] ?? null,
                ]
            );
            return response()->json(['msg' => 'ok']);
        }

        if ($accion === 'eliminar') {
            Alumno::where('idAlumno', $datos['idAlumno'])->delete();
            return response()->json(true);
        }

        return response()->json(['msg' => 'Accion no reconocida']);
    }
}
