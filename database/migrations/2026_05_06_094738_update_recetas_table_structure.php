<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('recetas', function (Blueprint $table) {
            // Eliminar columnas antiguas
            $table->dropColumn(['nombre', 'descripcion', 'pasos', 'tipo', 'tiempo', 'dificultad']);
            
            // Añadir nuevas columnas
            $table->string('titulo');
            $table->json('cuerpo');
            $table->boolean('es_favorito')->default(false);
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recetas', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['titulo', 'cuerpo', 'es_favorito', 'user_id']);
            
            // Restaurar columnas antiguas
            $table->string('nombre');
            $table->text('descripcion');
            $table->text('pasos');
            $table->string('tipo');
            $table->integer('tiempo');
            $table->string('dificultad');
        });
    }
};
