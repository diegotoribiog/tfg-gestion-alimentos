<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alimento extends Model
{
    // Campos rellenables masivamente
    protected $fillable = [
        'nombre', 
        'cantidad', 
        'unidad', 
        'fecha_caducidad', 
        'notas',
        'stock_minimo',
        'usuario_id', 
        'categoria_id'
    ];

    // Capitalizamos el nombre antes de guardarlo en la base de datos
    protected function setNombreAttribute($value)
    {
        $this->attributes['nombre'] = ucfirst(mb_strtolower(trim($value)));
    }

    // Relación: Un alimento pertenece a un usuario
    public function usuario() 
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // Relación: Un alimento pertenece a una categoría
    public function categoria() 
    {
        return $this->belongsTo(Categoria::class);
    }
}