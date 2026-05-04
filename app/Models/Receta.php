<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Receta extends Model
{
    // Una receta tiene muchos ingredientes y un ingrediente está en muchas recetas
    public function ingredientes() {
        return $this->belongsToMany(Ingrediente::class, 'receta_ingrediente');
    }
}