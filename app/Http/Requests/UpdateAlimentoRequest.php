<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAlimentoRequest extends FormRequest
{
    // Verificamos que el alimento pertenezca al usuario
    public function authorize(): bool
    {
        $alimento = $this->route('alimento');
        return $alimento && $alimento->usuario_id === $this->user()->id;
    }

    // Reglas de validación para actualizar un alimento
    public function rules(): array
    {
        return [
            'nombre' => ['sometimes', 'required', 'string', 'max:255'],
            'cantidad' => ['sometimes', 'required', 'numeric', 'min:0'],
            'unidad' => ['sometimes', 'required', 'string', 'max:10'],
            'fecha_caducidad' => ['sometimes', 'required', 'date'],
            'notas' => ['nullable', 'string'],
            'stock_minimo' => ['nullable', 'numeric', 'min:0'],
            'categoria_id' => ['sometimes', 'required', 'exists:categorias,id'],
        ];
    }
}
