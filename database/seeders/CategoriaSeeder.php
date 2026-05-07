<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categoria;
use Illuminate\Support\Facades\DB;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        // Limpieza de tablas con bypass de llaves foráneas
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Categoria::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Categorías densas y puramente alimentarias
        $categorias = [
            'Lácteos y Huevos',
            'Carnes y Embutidos',
            'Pescados y Mariscos',
            'Frutas y Verduras',
            'Legumbres, Arroz y Pasta',
            'Panadería y Dulces',
            'Congelados y Platos Listos',
            'Otros'
        ];

        foreach ($categorias as $nombre) {
            Categoria::create(['nombre' => $nombre]);
        }
    }
}
