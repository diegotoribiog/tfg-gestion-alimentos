<?php

namespace App\Http\Controllers;

use App\Models\Alimento;
use App\Models\Categoria;
use App\Http\Requests\StoreAlimentoRequest;
use App\Http\Requests\UpdateAlimentoRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class AlimentoController extends Controller
{
    // Muestra la lista de alimentos del usuario actual
    public function index(): Response
    {
        return Inertia::render('Alimentos/Inventario', [
            'alimentos' => auth()->user()->alimentos()->with('categoria')->get(),
            'categorias' => Categoria::all()
        ]);
    }

    // Guarda un nuevo alimento validado
    public function store(StoreAlimentoRequest $request): RedirectResponse
    {
        // El usuario_id se asigna automáticamente a través de la relación
        auth()->user()->alimentos()->create($request->validated());

        return redirect()->back()->with('message', 'Alimento guardado correctamente');
    }

    // Actualiza un alimento existente con validación y seguridad
    public function update(UpdateAlimentoRequest $request, Alimento $alimento): RedirectResponse
    {
        $alimento->update($request->validated());

        return redirect()->back()->with('message', 'Alimento actualizado');
    }

    // Elimina un alimento si pertenece al usuario
    public function destroy(Alimento $alimento): RedirectResponse
    {
        if ($alimento->usuario_id !== auth()->id()) {
            abort(403);
        }

        $alimento->delete();

        return redirect()->back()->with('message', 'Alimento eliminado');
    }

    // Lógica para ajustar stock (sumar o restar cantidad)
    public function ajustarStock(\Illuminate\Http\Request $request, Alimento $alimento): RedirectResponse
    {
        if ($alimento->usuario_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'cantidad' => ['required', 'numeric'],
        ]);

        $nuevaCantidad = (int)$alimento->cantidad + (int)$request->cantidad;

        if ($nuevaCantidad <= 0) {
            $alimento->delete();
            return redirect()->back()->with('message', 'Producto agotado y eliminado del inventario');
        }

        $alimento->update(['cantidad' => $nuevaCantidad]);

        return redirect()->back()->with('message', 'Stock actualizado correctamente');
    }
}
