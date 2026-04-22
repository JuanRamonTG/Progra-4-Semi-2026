<?php

namespace App\Http\Controllers;

use App\Models\RegistroFallecido;
use Illuminate\Http\Request;

class RegistroFallecidoController extends Controller
{
    public function index(Request $request)
    {
        $accion = $request->input('accion', '');

        if ($accion === 'consultar') {
            $query = RegistroFallecido::query();

            // Búsqueda por ubicación
            if ($request->filled('ubicacion')) {
                $query->where('ubicacion', 'like', '%' . $request->input('ubicacion') . '%');
            }

            // Búsqueda por descripción
            if ($request->filled('descripcion')) {
                $query->where('descripcion', 'like', '%' . $request->input('descripcion') . '%');
            }

            // Búsqueda por fecha
            if ($request->filled('fecha_desde')) {
                $query->whereDate('fecha_hora', '>=', $request->input('fecha_desde'));
            }

            if ($request->filled('fecha_hasta')) {
                $query->whereDate('fecha_hora', '<=', $request->input('fecha_hasta'));
            }

            // Búsqueda por estado
            if ($request->filled('estado')) {
                $query->where('estado', 'like', '%' . $request->input('estado') . '%');
            }

            // Búsqueda por verificación
            if ($request->filled('verificacion')) {
                $query->where('verificacion', $request->boolean('verificacion'));
            }

            return response()->json($query->orderBy('fecha_hora', 'desc')->get());
        }

        // Obtener datos del formulario
        $datosRaw = $request->input('registrofallecido');
        if (!$datosRaw) {
            return response()->json(['msg' => 'No hay datos', 'success' => false], 400);
        }

        $datos = is_array($datosRaw) ? $datosRaw : json_decode($datosRaw, true);
        if (!$datos) {
            return response()->json(['msg' => 'Datos inválidos', 'success' => false], 400);
        }

        // Validaciones básicas
        if (empty(trim($datos['fecha_hora'] ?? ''))) {
            return response()->json(['msg' => 'La fecha y hora es requerida', 'success' => false], 400);
        }

        if (empty(trim($datos['ubicacion'] ?? ''))) {
            return response()->json(['msg' => 'La ubicación es requerida', 'success' => false], 400);
        }

        if ($accion === 'nuevo' || $accion === 'modificar') {
            try {
                RegistroFallecido::updateOrCreate(
                    ['id' => $datos['id'] ?? null],
                    [
                        'fecha_hora' => $datos['fecha_hora'],
                        'ubicacion' => $datos['ubicacion'],
                        'descripcion' => $datos['descripcion'] ?? null,
                        'verificacion' => $datos['verificacion'] ?? false,
                        'fotos' => $datos['fotos'] ?? null,
                        'testigos' => $datos['testigos'] ?? null,
                        'hora_fallecimiento' => $datos['hora_fallecimiento'] ?? null,
                        'estado' => $datos['estado'] ?? null,
                    ]
                );
                return response()->json(['msg' => 'Registro guardado exitosamente', 'success' => true]);
            } catch (\Exception $e) {
                return response()->json(['msg' => 'Error al guardar: ' . $e->getMessage(), 'success' => false], 500);
            }
        }

        if ($accion === 'eliminar') {
            try {
                $id = $datos['id'] ?? null;
                if (!$id) {
                    return response()->json(['msg' => 'ID no válido', 'success' => false], 400);
                }
                RegistroFallecido::find($id)->delete();
                return response()->json(['msg' => 'Registro eliminado exitosamente', 'success' => true]);
            } catch (\Exception $e) {
                return response()->json(['msg' => 'Error al eliminar: ' . $e->getMessage(), 'success' => false], 500);
            }
        }

        return response()->json(['msg' => 'Acción no reconocida', 'success' => false], 400);
    }
}
