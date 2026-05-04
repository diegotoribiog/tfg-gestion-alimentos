<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ingrediente extends Model
{
    protected $fillable = ['nombre'];

    public function recetas() {
        return $this->belongsToMany(Receta::class, 'receta_ingrediente');
    }
}