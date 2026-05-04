<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Creamos un usuario de prueba (Email: test@example.com | Password: password)
        User::factory()->create([
            'name' => 'Diego',
            'email' => 'test@example.com',
        ]);

        // 2. Llamamos al Seeder de categorías
        $this->call([
            CategoriaSeeder::class,
        ]);
    }
}