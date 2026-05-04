<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAlimentoRequest extends FormRequest
{
    // Solo permitimos la acción si el usuario está autenticado
    public function authorize(): bool
    {
        return true; // El middleware auth ya se encarga de la seguridad general
    }

    // Reglas de validación para crear un alimento
    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255'],
            'cantidad' => ['required', 'numeric', 'min:0'],
            'unidad' => ['required', 'string', 'max:10'],
            'fecha_caducidad' => ['required', 'date'],
            'notas' => ['nullable', 'string'],
            'stock_minimo' => ['nullable', 'numeric', 'min:0'],
            'categoria_id' => ['required', 'exists:categorias,id'],
        ];
    }
}
