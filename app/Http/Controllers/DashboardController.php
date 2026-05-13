<?php

namespace App\Http\Controllers;

use App\Models\Alimento;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Carbon\Carbon;

class DashboardController extends Controller
{
    // Carga las estadísticas principales para el dashboard
    public function index(): Response
    {
        $userId = Auth::id();
        $isNewUser = session('is_new_user', false);
        $hoy = Carbon::now()->startOfDay();
        $limiteNaranja = Carbon::now()->addDays(4)->endOfDay();

        // Contamos alimentos según su estado de caducidad (ESTÁNDAR TFG)
        // ROJO (Ya caducado): Menos de 0 días
        $caducados = Alimento::where('usuario_id', $userId)
            ->where('fecha_caducidad', '<', $hoy)
            ->count();

        // NARANJA (Próximo): De 0 a 4 días (Incluye hoy)
        $proximos = Alimento::where('usuario_id', $userId)
            ->whereBetween('fecha_caducidad', [$hoy, $limiteNaranja])
            ->count();

        // VERDE (Bueno): 5 días o más
        $buenos = Alimento::where('usuario_id', $userId)
            ->where('fecha_caducidad', '>', $limiteNaranja)
            ->count();

        // Obtenemos los productos urgentes (Rojos y Naranjas) para alertas
        $productosUrgentes = Alimento::where('usuario_id', $userId)
            ->where('fecha_caducidad', '<=', $limiteNaranja)
            ->with('categoria')
            ->orderBy('fecha_caducidad', 'asc')
            ->get();

        return Inertia::render('Inicio', [
            'total' => $caducados + $proximos + $buenos,
            'alertaCaducidad' => $caducados + $proximos,
            'conteo' => [
                'bueno' => $buenos,
                'proximo' => $proximos,
                'caducado' => $caducados,
            ],
            'productosUrgentes' => $productosUrgentes,
            'isNewUser' => $isNewUser,
        ]);
    }
}
