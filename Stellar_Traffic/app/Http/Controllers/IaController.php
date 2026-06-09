<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IaController extends Controller
{
    /**
     * Endpoint principal: recibe una descripción en texto libre y retorna
     * un JSON estructurado con los datos del incidente extraídos por Gemini.
     */
    public function parsearIncidente(Request $request)
    {
        $request->validate([
            'descripcion' => 'required|string|min:10',
        ]);

        $textoLibre = $request->input('descripcion');
        $apiKey = config('services.gemini.api_key', env('GEMINI_API_KEY'));

        if (empty($apiKey)) {
            Log::info('Gemini API Key no configurada. Retornando respuesta simulada.');
            return response()->json($this->obtenerRespuestaSimulada($textoLibre));
        }

        try {
            $data = $this->llamarGemini($apiKey, $textoLibre);
            return response()->json($this->normalizarRespuesta($data));
        } catch (\Exception $e) {
            Log::error('Excepción en IaController: ' . $e->getMessage());
            
            // Fallback a simulación si Gemini falla
            Log::info('Utilizando fallback de simulación local.');
            return response()->json($this->obtenerRespuestaSimulada($textoLibre));
        }
    }

    /**
     * Realiza la llamada HTTP a la API de Gemini usando systemInstruction
     * para separar el contexto del sistema del mensaje del usuario.
     */
    private function llamarGemini(string $apiKey, string $textoUsuario): array
    {
        $fechaHoy = date('Y-m-d');
        $horaActual = date('H:i');

        $response = Http::timeout(30)
            ->retry(2, 1000)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
                'systemInstruction' => [
                    'parts' => [
                        ['text' => $this->obtenerPromptSistema($fechaHoy, $horaActual)]
                    ]
                ],
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => $textoUsuario]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'responseMimeType' => 'application/json',
                    'temperature' => 0.2,
                ]
            ]);

        if (!$response->successful()) {
            $errorBody = $response->body();
            Log::error("Gemini API HTTP {$response->status()}: {$errorBody}");
            throw new \RuntimeException("Gemini API respondió con HTTP {$response->status()}");
        }

        $resultadoJson = $response->json('candidates.0.content.parts.0.text');

        if (empty($resultadoJson)) {
            Log::error('Gemini no retornó contenido en candidates.0.content.parts.0.text');
            throw new \RuntimeException('Respuesta vacía de Gemini');
        }

        // Limpiar delimitadores de markdown por precaución
        $resultadoJson = preg_replace('/^```json\s*/i', '', $resultadoJson);
        $resultadoJson = preg_replace('/\s*```$/', '', $resultadoJson);
        $resultadoJson = trim($resultadoJson);

        $data = json_decode($resultadoJson, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('JSON inválido de Gemini: ' . $resultadoJson);
            throw new \RuntimeException('Gemini no retornó JSON válido: ' . json_last_error_msg());
        }

        return $data;
    }

    /**
     * Prompt de sistema optimizado que define el rol de la IA y la estructura exacta
     * del JSON de salida. Se inyecta la fecha y hora actual para mejorar la precisión.
     */
    private function obtenerPromptSistema(string $fechaHoy, string $horaActual): string
    {
        return <<<PROMPT
Eres un asistente de la Policía Nacional Civil (PNC) de El Salvador especializado en analizar reportes de accidentes de tránsito.

Tu tarea es extraer información estructurada de la descripción de un accidente proporcionada por un agente policial o testigo.

REGLAS:
- Devuelve SOLO un objeto JSON válido, sin texto explicativo, sin delimitadores de código.
- Si un dato no está explícitamente mencionado, intenta deducirlo del contexto. Si no es posible deducirlo, usa los valores por defecto indicados.
- La fecha de hoy es: {$fechaHoy}. La hora actual es: {$horaActual}.
- Todos los textos deben estar en español.

ESTRUCTURA JSON REQUERIDA:
{
  "tipo_accidente": "victimas" o "materiales" ("victimas" si hay lesionados, heridos o fallecidos; "materiales" si solo hay daños a vehículos/propiedad),
  "fecha_incidente": "YYYY-MM-DD" (si dicen "hoy", usar {$fechaHoy}; si dicen "ayer", restar un día; si mencionan una fecha, formatearla),
  "hora_aproximada": "HH:MM" (formato 24h; si no se menciona hora, usar "{$horaActual}"),
  "gravedad": "Leve" o "Grave" ("Grave" si hay heridos, fallecidos, vehículos volcados o daños estructurales; "Leve" en caso contrario),
  "direccion": "dirección, intersección o referencia geográfica del lugar del incidente" (valor por defecto: "No especificada"),
  "municipio": "municipio, distrito o ciudad" (valor por defecto: "No especificado"),
  "descripcion": "resumen ejecutivo conciso de lo sucedido, redactado en tercera persona",
  "condicion_climatica": "Despejado", "Lluvia", "Niebla", "Nublado" o "Viento" (deducir de pistas como "mojado", "lloviendo", etc.; por defecto: "Despejado"),
  "tipo_via": "Carretera", "Avenida", "Calle", "Autopista", "Boulevard" o "Camino" (deducir del contexto; por defecto: "Calle"),
  "estado_pavimento": "Seco", "Mojado" o "Resbaladizo" (si llueve o mencionan agua, "Mojado"; por defecto: "Seco"),
  "declaracion_involucrados": "síntesis de lo declarado por los involucrados o testigos, en tercera persona" (si no hay declaraciones, resumir los hechos),
  "vehiculos": ["descripción corta de cada vehículo involucrado, incluyendo placa si se menciona"],
  "personas": ["nombre y rol de cada persona mencionada (conductor, herido, testigo)"]
}
PROMPT;
    }

    /**
     * Normaliza la respuesta de Gemini asegurándose de que todos los campos esperados existan
     * y tengan el tipo de dato correcto.
     */
    private function normalizarRespuesta(array $data): array
    {
        $defaults = [
            'tipo_accidente' => 'materiales',
            'fecha_incidente' => date('Y-m-d'),
            'hora_aproximada' => date('H:i'),
            'gravedad' => 'Leve',
            'direccion' => 'No especificada',
            'municipio' => 'No especificado',
            'descripcion' => '',
            'condicion_climatica' => 'Despejado',
            'tipo_via' => 'Calle',
            'estado_pavimento' => 'Seco',
            'declaracion_involucrados' => '',
            'vehiculos' => [],
            'personas' => [],
        ];

        $resultado = [];
        foreach ($defaults as $key => $default) {
            $valor = $data[$key] ?? $default;
            
            // Asegurar que arrays sean arrays
            if (is_array($default) && !is_array($valor)) {
                $valor = is_string($valor) ? [$valor] : $default;
            }
            
            // Asegurar que strings sean strings
            if (is_string($default) && !is_string($valor)) {
                $valor = is_array($valor) ? implode(', ', $valor) : (string) $valor;
            }
            
            $resultado[$key] = $valor;
        }

        // Normalizar tipo_accidente a valores válidos
        if (!in_array($resultado['tipo_accidente'], ['victimas', 'materiales'])) {
            $resultado['tipo_accidente'] = 'materiales';
        }

        // Normalizar gravedad
        if (!in_array($resultado['gravedad'], ['Leve', 'Grave'])) {
            $resultado['gravedad'] = 'Leve';
        }

        return $resultado;
    }

    /**
     * Simulación local inteligente basada en keywords del texto.
     * Se usa como fallback cuando no hay API Key o cuando Gemini falla.
     */
    private function obtenerRespuestaSimulada(string $texto): array
    {
        $esVictima = (bool) preg_match('/herido|lesion|muert|falleci|atropell|hospital/i', $texto);
        $esGrave = $esVictima || (bool) preg_match('/grave|fuerte|volcó|volcadura|incendi|fatal/i', $texto);

        // Detectar clima
        $clima = 'Despejado';
        if (preg_match('/lluv|llov|tormenta|aguacero/i', $texto)) {
            $clima = 'Lluvia';
        } elseif (preg_match('/niebla|neblina|bruma/i', $texto)) {
            $clima = 'Niebla';
        } elseif (preg_match('/nublado|nubla/i', $texto)) {
            $clima = 'Nublado';
        }

        // Detectar pavimento
        $pavimento = 'Seco';
        if (preg_match('/mojado|moja|lluv|llov|agua|charco|resbal/i', $texto)) {
            $pavimento = 'Mojado';
        }

        // Extraer dirección
        $direccion = 'No especificada';
        if (preg_match('/(?:en la|en el|sobre la|sobre|por la|por el|frente a|cerca de)\s+([A-Za-zÁÉÍÓÚáéíóúñÑ0-9\.\s,]+?)(?:\.|,|;|bajo|a las|con |donde|$)/iu', $texto, $m)) {
            $direccion = trim($m[1]);
        }

        // Extraer municipio
        $municipio = 'No especificado';
        if (preg_match('/(?:municipio de|municipio|distrito de|ciudad de|en)\s+([A-Za-zÁÉÍÓÚáéíóúñÑ\s]+?)(?:\.|,|;|$)/iu', $texto, $m)) {
            $municipio = trim($m[1]);
        }

        // Extraer vehículos
        $vehiculos = [];
        if (preg_match_all('/(?:corolla|toyota|nissan|honda|hyundai|kia|suzuki|mazda|chevrolet|ford|moto(?:cicleta)?|camión|sedán|sedan|camioneta|pick\s*up|bus|microbús|taxi|carro|vehículo|vehiculo|bicicleta)\s*[a-záéíóúñ]*/iu', $texto, $matches)) {
            $vehiculos = array_values(array_unique(array_map('trim', $matches[0])));
        }
        if (empty($vehiculos)) {
            $vehiculos = ['Vehículo A', 'Vehículo B'];
        }

        // Extraer tipo de vía
        $tipoVia = 'Calle';
        if (preg_match('/carretera|autopista|panamericana/i', $texto)) {
            $tipoVia = 'Carretera';
        } elseif (preg_match('/avenida|av\./i', $texto)) {
            $tipoVia = 'Avenida';
        } elseif (preg_match('/boulevard|blvd/i', $texto)) {
            $tipoVia = 'Boulevard';
        }

        return [
            'tipo_accidente' => $esVictima ? 'victimas' : 'materiales',
            'fecha_incidente' => date('Y-m-d'),
            'hora_aproximada' => date('H:i'),
            'gravedad' => $esGrave ? 'Grave' : 'Leve',
            'direccion' => $direccion,
            'municipio' => $municipio,
            'descripcion' => mb_substr($texto, 0, 300),
            'condicion_climatica' => $clima,
            'tipo_via' => $tipoVia,
            'estado_pavimento' => $pavimento,
            'declaracion_involucrados' => 'Reporte registrado: ' . mb_substr($texto, 0, 250),
            'vehiculos' => $vehiculos,
            'personas' => ['Conductor A', 'Conductor B'],
        ];
    }
}
