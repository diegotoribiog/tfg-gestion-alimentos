<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    // Una categoría puede tener muchos alimentos (ej: Fruta -> Manzana, Pera)
    public function alimentos() {
        return $this->hasMany(Alimento::class);
    }
}