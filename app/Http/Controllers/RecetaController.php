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
        $alimentosIds = $request->input('alimentos_ids', []); // Array de IDs
        $alimentoIdUnico = $request->input('alimento_id'); // Mantener compatibilidad

        // Combinar IDs si vienen ambos
        if ($alimentoIdUnico) {
            $alimentosIds[] = $alimentoIdUnico;
        }
        $alimentosIds = array_unique($alimentosIds);

        try {
            // 1. Obtener alimentos con STOCK REAL (>0)
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

            // 2. Selección de Ingredientes Mandatorios
            $ingredientesMandatorios = collect();
            
            // Si el usuario seleccionó manualmente desde inventario, permitimos caducados si así lo desea
            if (!empty($alimentosIds)) {
                $ingredientesMandatorios = $queryAlimentos->whereIn('id', $alimentosIds);
            } else {
                // GENERACIÓN RÁPIDA: Filtrar estrictamente para excluir caducados
                $queryAlimentos = $queryAlimentos->filter(function($a) use ($hoy) {
                    return Carbon::parse($a->fecha_caducidad)->startOfDay()->gte($hoy->startOfDay());
                });

                if ($queryAlimentos->isEmpty()) {
                    return response()->json([
                        'error' => 'No tienes alimentos vigentes (no caducados) para una receta rápida. Por favor, selecciona ingredientes manualmente desde el inventario.'
                    ], 400);
                }
            }

            // Categorías de proteína pesada
            $proteinasCategorias = ['Carnes y Embutidos', 'Pescados y Mariscos'];

            // Si no hay mandatorios, buscamos la proteína que caduca antes (ya filtrado por no caducados arriba)
            if ($ingredientesMandatorios->isEmpty()) {
                $proteinaProxima = $queryAlimentos->first(function($a) use ($proteinasCategorias) {
                        return in_array($a->categoria?->nombre, $proteinasCategorias);
                    });
                if ($proteinaProxima) {
                    $ingredientesMandatorios->push($proteinaProxima);
                }
            }

            // Filtrado de contexto para la IA
            $alimentosParaIA = collect();
            if ($ingredientesMandatorios->isNotEmpty()) {
                $alimentosParaIA = $ingredientesMandatorios->map(function($a) { return $a; });
                
                $tieneProteina = $ingredientesMandatorios->contains(function($a) use ($proteinasCategorias) {
                    return in_array($a->categoria?->nombre, $proteinasCategorias);
                });
                
                $acompanantes = $queryAlimentos->filter(function($a) use ($ingredientesMandatorios, $proteinasCategorias, $tieneProteina) {
                    if ($ingredientesMandatorios->pluck('id')->contains($a->id)) return false;
                    
                    // Si ya tenemos proteína, evitamos más proteínas pesadas
                    if ($tieneProteina && in_array($a->categoria?->nombre, $proteinasCategorias)) {
                        return false;
                    }
                    
                    return true;
                })
                ->take(5);
                
                $alimentosParaIA = $alimentosParaIA->merge($acompanantes);
            } else {
                $alimentosParaIA = $queryAlimentos->take(6);
            }

            // Preparar lista técnica para la IA
            $contextoIngredientes = $alimentosParaIA->map(function ($a) {
                return "ID: {$a->id} - Nombre: {$a->nombre}, Stock: {$a->cantidad} {$a->unidad}, Cat: {$a->categoria?->nombre}";
            })->implode('; ');

            $apiKey = config('services.groq.api_key');
            if (!$apiKey) return response()->json(['error' => 'Configuración de IA no encontrada en el servidor.'], 500);

            // Ajuste del mensaje
            $nombresMandatorios = $ingredientesMandatorios->pluck('nombre')->implode(', ');
            $mensajeUsuario = $ingredientesMandatorios->isNotEmpty() 
                ? "DEBES incluir obligatoriamente los ingredientes [{$nombresMandatorios}] como base de la receta. Acompáñalos con otros ingredientes del contexto [{$contextoIngredientes}]."
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
                                1. LOS INGREDIENTES SELECCIONADOS SON MANDATORIOS, TIENES QUE UTILIZARLOS SI O SI.
                                2. CONDIMENTOS LIBRES: Tienes permiso total para usar aceite, sal, especias, vinagre y salsas básicas aunque no estén en el inventario. Inclúyelos en 'ingredientes_usados' con id: null y es_basico: true.
                                3. Usa preferiblemente entre 3 y 8 ingredientes del inventario total proporcionado (además de los condimentos). Intenta que las recetas incluyan varios ingredientes del inventario y que por ejemplo al hacer ensaladas no sean muy simples (por ejemplo utiliza varias verduras como lechuga, pepino, tomates, etc).
                                4. No mezcles proteínas pesadas (carne/pescado).
                                5. No mezcles sabores incompatibles.
                                6. NUNCA uses más cantidad de la que hay disponible en el inventario para un ingrediente.
                                7. RESTRICCIÓN DE STOCK: Está terminantemente PROHIBIDO inventar ingredientes en 'ingredientes_usados'. Solo puedes incluir ingredientes que aparezcan explícitamente en el contexto [{$contextoIngredientes}] o condimentos básicos. Cualquier otro ingrediente necesario debe ir en 'ingredientes_extras'.
                                
                                REGLAS TÉCNICAS:
                                - Unidades: GR, ML, UD (siempre enteros). PROHIBIDO realizar conversiones de unidades: Si un ingrediente está en 'GR' en el inventario, úsalo obligatoriamente en 'GR' en la receta. No cambies 'GR' o 'ML' por 'UD' ni viceversa.
                                - Cantidades: NUNCA uses 0. Incluso para condimentos o básicos, pon una cantidad lógica para 1 persona (ej: 10 ML, 5 GR, 1 UD).
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

                if ($response->failed()) {
                    Log::error("Groq API error: " . $response->body());
                    continue;
                }

                $data = $response->json();
                $content = $data['choices'][0]['message']['content'] ?? null;
                if (!$content) continue;

                $recetaCuerpo = json_decode($content, true);

                // Si no hay ingredientes mandatorios, aceptamos
                if ($ingredientesMandatorios->isEmpty()) break;

                // Verificar mandatorios
                $idsUsados = collect($recetaCuerpo['ingredientes_usados'])->pluck('id')->filter()->toArray();
                $todosPresentes = true;
                foreach ($ingredientesMandatorios as $m) {
                    if (!in_array($m->id, $idsUsados)) {
                        $todosPresentes = false;
                        break;
                    }
                }

                if ($todosPresentes) break; 
                
                $mensajeUsuario = "¡ERROR! Olvidaste incluir los ingredientes obligatorios [{$nombresMandatorios}]. REINTENTA incluyéndolos todos en 'ingredientes_usados'.";
            }

            if (!$recetaCuerpo) return response()->json(['error' => 'No se pudo generar la receta. Por favor, inténtalo de nuevo.'], 500);

            if ($recetaCuerpo['titulo'] === 'ERROR_GASTRONOMICO') {
                return response()->json([
                    'error' => 'No se pudo crear una receta coherente con estos ingredientes.'
                ], 422);
            }

            // Gestionar límite de historial (9 recetas visibles en el historial)
            // Contamos las recetas que se muestran en el historial: las que no son favoritas Y las favoritas que no están ocultas
            $historialVisibleCount = Receta::where('user_id', $userId)
                ->where(function($query) {
                    $query->where('es_favorito', false)
                          ->orWhere(function($q) {
                              $q->where('es_favorito', true)
                                ->where('ocultar_en_historial', false);
                          });
                })
                ->count();

            if ($historialVisibleCount >= 9) {
                // Buscamos la más antigua que esté actualmente visible en el historial
                $recetaParaGestionar = Receta::where('user_id', $userId)
                    ->where(function($query) {
                        $query->where('es_favorito', false)
                              ->orWhere(function($q) {
                                  $q->where('es_favorito', true)
                                    ->where('ocultar_en_historial', false);
                              });
                    })
                    ->orderBy('created_at', 'asc')
                    ->first();

                if ($recetaParaGestionar) {
                    if ($recetaParaGestionar->es_favorito) {
                        // Si es favorita, solo la ocultamos del historial para no borrarla de favoritos
                        $recetaParaGestionar->update(['ocultar_en_historial' => true]);
                    } else {
                        // Si no es favorita, la borramos definitivamente
                        $recetaParaGestionar->delete();
                    }
                }
            }

            // Guardamos el cuerpo COMPLETO que viene de la IA (incluyendo básicos y extras)
            $nuevaReceta = Receta::create([
                'titulo' => $recetaCuerpo['titulo'],
                'cuerpo' => $recetaCuerpo, // El casting AsArrayObject en el modelo manejará la persistencia completa
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
            Log::error("Error Generar Receta: " . $e->getMessage());
            return response()->json(['error' => 'Error al procesar la receta.'], 500);
        }
    }

    public function destroyHistory()
    {
        $userId = Auth::id();

        // 1. Las que NO son favoritas se borran definitivamente
        Receta::where('user_id', $userId)
            ->where('es_favorito', false)
            ->delete();

        // 2. Las que SÍ son favoritas se marcan como ocultas en el historial
        Receta::where('user_id', $userId)
            ->where('es_favorito', true)
            ->update(['ocultar_en_historial' => true]);
        
        return back()->with('message', 'Historial limpiado correctamente');
    }

    public function cocinarReceta(Request $request)
    {
        $request->validate([
            'ingredientes' => 'required|array',
            // Admitimos que algunos no tengan id (básicos) pero tengan cantidad_valor
        ]);

        try {
            foreach ($request->ingredientes as $item) {
                // Solo descontamos si tiene ID (no es un condimento básico/staple)
                if (isset($item['id']) && !empty($item['id'])) {
                    $alimento = Alimento::find($item['id']);
                    if ($alimento) {
                        $cantidadARestar = (float)($item['cantidad_valor'] ?? 0);
                        $nuevaCantidad = (float)$alimento->cantidad - $cantidadARestar;
                        
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

    public function toggleFavorito($id)
    {
        $userId = Auth::id();
        $recetaOriginal = Receta::where('user_id', $userId)->findOrFail($id);
        
        // Si ya es favorita, simplemente la desmarcamos
        if ($recetaOriginal->es_favorito) {
            $recetaOriginal->update(['es_favorito' => false]);
            
            // Si además estaba oculta en el historial (porque el usuario la borró de ahí), 
            // al dejar de ser favorita ya no tiene sentido mantenerla oculta (se borra del todo)
            if ($recetaOriginal->ocultar_en_historial) {
                $recetaOriginal->delete();
                return response()->json(['es_favorito' => false, 'deleted' => true]);
            }

            return response()->json(['es_favorito' => false]);
        }

        // Si no es favorita, la marcamos como tal.
        $recetaOriginal->update(['es_favorito' => true]);
        
        return response()->json(['es_favorito' => true]);
    }

    public function destroy($id)
    {
        $userId = Auth::id();
        $receta = Receta::where('user_id', $userId)->findOrFail($id);

        // Si la receta es favorita, no la borramos de la DB, solo la ocultamos del historial
        if ($receta->es_favorito) {
            $receta->update(['ocultar_en_historial' => true]);
        } else {
            // Si no es favorita, se borra permanentemente
            $receta->delete();
        }

        return back();
    }
}
