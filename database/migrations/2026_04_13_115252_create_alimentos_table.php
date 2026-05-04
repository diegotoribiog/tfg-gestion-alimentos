<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alimentos', function (Blueprint $table) {
            $table->id(); // ID del alimento
            $table->string('nombre'); // Nombre del producto
            $table->integer('cantidad'); // Cantidad disponible
            $table->date('fecha_caducidad'); // Fecha de vencimiento
            
            // Relación con el Usuario (quién es el dueño del alimento)
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
            
            // Relación con la Categoría
            $table->foreignId('categoria_id')->constrained('categorias');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alimentos');
    }
};