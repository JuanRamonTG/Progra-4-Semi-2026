<?php

namespace App\Http\Controllers;

use App\Models\Accidente;
<<<<<<< HEAD
=======
use App\Models\VehiculoInvolucrado;
use App\Models\PersonaInvolucrada;
>>>>>>> origin/main
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccidenteController extends Controller
{
    public function index(Request $request)
    {
        $accidentes = Accidente::all();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($accidentes);
        }

        return view('accidentes.index', compact('accidentes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tipo_accidente' => 'required|string',
            'fecha_incidente' => 'required|date',
            'hora_aproximada' => 'nullable|string',
            'gravedad' => 'nullable|string',
            'direccion' => 'nullable|string',
            'municipio' => 'nullable|string',
            'id_caso' => 'required|string|unique:accidentes,id_caso',
<<<<<<< HEAD
=======
            'descripcion' => 'nullable|string|max:500',
            'condicion_climatica' => 'nullable|string',
            'tipo_via' => 'nullable|string',
            'estado_pavimento' => 'nullable|string',
            'declaracion_involucrados' => 'nullable|string',
            'vehiculos' => 'nullable|array',
            'vehiculos.*' => 'string',
            'personas' => 'nullable|array',
            'personas.*' => 'string',
>>>>>>> origin/main
        ]);

        $data['id_usuario'] = Auth::id() ?? 1;
        
        $accidente = Accidente::create($data);

<<<<<<< HEAD
=======
        // Guardar vehículos involucrados
        if (!empty($request->vehiculos)) {
            foreach ($request->vehiculos as $vehiculo) {
                VehiculoInvolucrado::create([
                    'id_accidente' => $accidente->id_accidente,
                    // El frontend solo manda un string con la "Placa o descripción", 
                    // así que lo guardaremos temporalmente en el campo "marca" (o podrías crear un campo específico)
                    'marca' => $vehiculo, 
                ]);
            }
        }

        // Guardar personas involucradas
        if (!empty($request->personas)) {
            foreach ($request->personas as $persona) {
                PersonaInvolucrada::create([
                    'id_accidente' => $accidente->id_accidente,
                    'nombre_completo' => $persona, // El frontend manda "Nombre o descripción"
                ]);
            }
        }

>>>>>>> origin/main
        return response()->json(['message' => 'Accidente registrado con éxito', 'data' => $accidente]);
    }
}
