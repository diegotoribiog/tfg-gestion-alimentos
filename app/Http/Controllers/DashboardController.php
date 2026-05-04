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
        $hoy = Carbon::now()->startOfDay();
        $limite = Carbon::now()->addDays(7)->endOfDay();

        // Contamos alimentos según su estado de caducidad
        $caducados = Alimento::where('usuario_id', $userId)
            ->where('fecha_caducidad', '<', $hoy)
            ->count();

        $proximos = Alimento::where('usuario_id', $userId)
            ->whereBetween('fecha_caducidad', [$hoy, $limite])
            ->count();

        $buenos = Alimento::where('usuario_id', $userId)
            ->where('fecha_caducidad', '>', $limite)
            ->count();

        // Obtenemos los productos caducados o que caducan pronto para mostrar alertas
        $productosUrgentes = Alimento::where('usuario_id', $userId)
            ->where('fecha_caducidad', '<=', $limite)
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
        ]);
    }
}
