<?php

namespace App\Http\Controllers;

use App\Models\Alimento;
use App\Models\Receta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Inertia\Inertia;

class RecetaController extends Controller
{
    public function index()
    {
        $recetas = Receta::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Recetas/MisRecetas', [
            'recetas' => $recetas
        ]);
    }

    public function generar(Request $request)
    {
        $userId = Auth::id();
        $hoy = Carbon::now();
        $alimentoId = $request->input('alimento_id');

        try {
            // 1. Obtener alimentos con STOCK REAL (>0)
            // Incluimos los que están a punto de caducar o recién caducados para que el usuario pueda "salvarlos"
            $queryAlimentos = Alimento::where('usuario_id', $userId)
                ->where('cantidad', '>', 0)
                ->with('categoria')
                ->orderBy('fecha_caducidad', 'asc')
                ->get();

            if ($queryAlimentos->isEmpty()) {
                return response()->json([
                    'error' => 'No tienes alimentos con stock suficiente para una receta.'
                ], 400);
            }

            // 2. Selección de Ingrediente Principal y Acompañantes
            $ingredienteMandatorio = null;
            if ($alimentoId) {
                $ingredienteMandatorio = $queryAlimentos->firstWhere('id', $alimentoId);
            }

            // Categorías de proteína pesada
            $proteinasCategorias = ['Carnes y Embutidos', 'Pescados y Mariscos'];

            // Si no hay mandatorio, buscamos la proteína que caduca antes (que no haya caducado ya por mucho)
            if (!$ingredienteMandatorio) {
                $ingredienteMandatorio = $queryAlimentos->where('fecha_caducidad', '>=', $hoy->toDateString())
                    ->first(function($a) use ($proteinasCategorias) {
                        return in_array($a->categoria?->nombre, $proteinasCategorias);
                    });
            }

            // Filtrado de contexto para la IA: AISLAMIENTO DE PROTEÍNA
            $alimentosParaIA = collect();
            if ($ingredienteMandatorio) {
                $alimentosParaIA->push($ingredienteMandatorio);
                
                $esProteina = in_array($ingredienteMandatorio->categoria?->nombre, $proteinasCategorias);
                
                // Si el principal es proteína, FILTRAMOS TODAS las demás proteínas pesadas del contexto
                // para que la IA ni siquiera las vea y no pueda mezclarlas.
                $acompanantes = $queryAlimentos->filter(function($a) use ($ingredienteMandatorio, $proteinasCategorias, $esProteina) {
                    if ($a->id === $ingredienteMandatorio->id) return false;
                    
                    // Si ya tenemos una proteína, eliminamos cualquier otra proteína pesada
                    if ($esProteina && in_array($a->categoria?->nombre, $proteinasCategorias)) {
                        return false;
                    }
                    
                    return true;
                })
                ->where('fecha_caducidad', '>=', $hoy->subDays(2)->toDateString())
                ->take(4);
                
                $alimentosParaIA = $alimentosParaIA->merge($acompanantes);
            } else {
                $alimentosParaIA = $queryAlimentos->take(5);
            }

            // Preparar lista técnica para la IA
            $contextoIngredientes = $alimentosParaIA->map(function ($a) {
                return "ID: {$a->id} - Nombre: {$a->nombre}, Stock: {$a->cantidad} {$a->unidad}, Cat: {$a->categoria?->nombre}";
            })->implode('; ');

            $apiKey = config('services.groq.api_key');
            if (!$apiKey) return response()->json(['error' => 'Configuración de IA no encontrada.'], 500);

            // Ajuste del mensaje: Obligatoriedad estricta
            $mensajeUsuario = $ingredienteMandatorio 
                ? "DEBES incluir obligatoriamente el ingrediente [{$ingredienteMandatorio->nombre}] (ID: {$ingredienteMandatorio->id}) como base de la receta. Acompáñalo con otros ingredientes del contexto [{$contextoIngredientes}] que tengan sentido gastronómico."
                : "Usa los ingredientes del contexto [{$contextoIngredientes}] para crear una receta lógica para una persona.";

            $intentos = 0;
            $maxIntentos = 2;
            $recetaCuerpo = null;

            while ($intentos < $maxIntentos) {
                $intentos++;
                
                $response = Http::timeout(40)->withToken($apiKey)
                    ->post('https://api.groq.com/openai/v1/chat/completions', [
                        'model' => 'llama-3.1-8b-instant',
                        'messages' => [
                            [
                                'role' => 'system',
                                'content' => 'Eres un chef de precisión. Tu misión es evitar el desperdicio alimentario creando recetas coherentes.'
                            ],
                            [
                                'role' => 'user',
                                'content' => "INSTRUCCIÓN CRÍTICA: {$mensajeUsuario}
                                
                                REGLAS DE ORO:
                                1. EL INGREDIENTE SELECCIONADO ES MANDATORIO si se especifica.
                                2. CONDIMENTOS LIBRES: Tienes permiso total para usar aceite, sal, especias, vinagre y salsas básicas aunque no estén en el inventario. Inclúyelos en 'ingredientes_usados' con id: null y es_basico: true.
                                3. Usa entre 3 y 5 ingredientes del inventario total proporcionado (además de los condimentos).
                                4. No mezcles proteínas pesadas (carne/pescado).
                                5. No mezcles sabores incompatibles.
                                
                                REGLAS TÉCNICAS:
                                - Unidades: GR, ML, UD (siempre enteros).
                                - Ración: 1 Persona.
                                - Si es imposible crear algo coherente, el título debe ser 'ERROR_GASTRONOMICO'.
                                
                                Responde exclusivamente con este JSON:
                                {
                                    \"titulo\": \"Nombre de la receta\",
                                    \"tiempo\": \"X min\",
                                    \"dificultad\": \"Fácil/Media/Difícil\",
                                    \"ingredientes_usados\": [
                                        {\"id\": id_o_null, \"nombre\": \"nombre\", \"cantidad_valor\": integer, \"unidad\": \"GR/ML/UD\", \"es_basico\": boolean}
                                    ],
                                    \"ingredientes_extras\": [
                                        {\"nombre\": \"nombre\", \"cantidad\": \"cantidad entera y unidad (GR/ML/UD)\"}
                                    ],
                                    \"pasos\": [\"paso corto\"]
                                }"
                            ]
                        ],
                        'temperature' => 0.1,
                        'response_format' => ['type' => 'json_object']
                    ]);

                if ($response->failed()) continue;

                $data = $response->json();
                $recetaCuerpo = json_decode($data['choices'][0]['message']['content'], true);

                // Si no hay ingrediente mandatorio, aceptamos la primera respuesta válida
                if (!$alimentoId) break;

                // Si hay ingrediente mandatorio, verificamos que esté en los usados
                $usado = collect($recetaCuerpo['ingredientes_usados'])->first(function($ing) use ($alimentoId) {
                    return isset($ing['id']) && $ing['id'] == $alimentoId;
                });

                if ($usado) break; // IA cumplió la orden
                
                // Si llegamos aquí, la IA ignoró el ingrediente mandatorio. Reintentamos con un mensaje más agresivo.
                $mensajeUsuario = "¡ERROR PREVIO! Olvidaste incluir el ingrediente obligatorio [{$ingredienteMandatorio->nombre}] (ID: {$alimentoId}). REINTENTA incluyendo este ingrediente SÍ O SÍ en 'ingredientes_usados'.";
            }

            if (!$recetaCuerpo) return response()->json(['error' => 'Servicio de cocina no disponible.'], 500);

            // Manejo de error gastronómico desde la IA
            if ($recetaCuerpo['titulo'] === 'ERROR_GASTRONOMICO') {
                $sugerencias = collect($recetaCuerpo['ingredientes_extras'] ?? [])->pluck('nombre')->implode(', ');
                return response()->json([
                    'error' => 'La combinación de ingredientes actual no es apta para una receta lógica. Sugerencia: ' . $sugerencias
                ], 422);
            }

            // Guardar automáticamente en el historial
            $nuevaReceta = Receta::create([
                'titulo' => $recetaCuerpo['titulo'],
                'cuerpo' => $recetaCuerpo,
                'user_id' => $userId,
                'es_favorito' => false,
            ]);

            return response()->json([
                'id' => $nuevaReceta->id,
                'titulo' => $nuevaReceta->titulo,
                'cuerpo' => $nuevaReceta->cuerpo,
                'es_favorito' => $nuevaReceta->es_favorito,
            ]);

        } catch (\Exception $e) {
            Log::error("Error Chef Senior: " . $e->getMessage());
            return response()->json(['error' => 'Error en la propuesta técnica.'], 500);
        }
    }

    public function cocinarReceta(Request $request)
    {
        $request->validate([
            'ingredientes' => 'required|array',
            'ingredientes.*.cantidad_valor' => 'required|integer', // Forzamos entero
        ]);

        try {
            foreach ($request->ingredientes as $item) {
                // Solo descontamos si tiene ID (no es un condimento básico/staple)
                if (isset($item['id']) && !empty($item['id'])) {
                    $alimento = Alimento::find($item['id']);
                    if ($alimento) {
                        $cantidadARestar = (int)$item['cantidad_valor'];
                        $nuevaCantidad = (int)$alimento->cantidad - $cantidadARestar;
                        
                        if ($nuevaCantidad <= 0) {
                            $alimento->delete();
                        } else {
                            $alimento->update(['cantidad' => $nuevaCantidad]);
                        }
                    }
                }
            }

            return response()->json(['success' => 'Inventario actualizado correctamente.']);
        } catch (\Exception $e) {
            Log::error("Error al descontar stock: " . $e->getMessage());
            return response()->json(['error' => 'Error técnico al actualizar el stock.'], 500);
        }
    }

    public function toggleFavorite($id)
    {
        $receta = Receta::where('user_id', Auth::id())->findOrFail($id);
        $receta->update(['es_favorito' => !$receta->es_favorito]);
        return response()->json(['es_favorito' => $receta->es_favorito]);
    }

    public function destroy($id)
    {
        Receta::where('user_id', Auth::id())->findOrFail($id)->delete();
        return back();
    }
}
