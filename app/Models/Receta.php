<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Receta extends Model
{
    protected $fillable = [
        'titulo',
        'cuerpo',
        'es_favorito',
        'user_id',
    ];

    protected $casts = [
        'cuerpo' => 'array',
        'es_favorito' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Una receta tiene muchos ingredientes (opcional, ya que ahora se guardan en el JSON, pero mantengo la relación si se usa en otro sitio)
    public function ingredientes() {
        return $this->belongsToMany(Ingrediente::class, 'receta_ingrediente');
    }
}