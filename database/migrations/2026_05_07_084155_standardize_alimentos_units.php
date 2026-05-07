<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Convertir datos existentes a las nuevas unidades estándar
        
        // KG -> GR
        DB::table('alimentos')
            ->whereIn('unidad', ['kg', 'KG', 'Kg'])
            ->update([
                'cantidad' => DB::raw('cantidad * 1000'),
                'unidad' => 'GR'
            ]);

        // L -> ML
        DB::table('alimentos')
            ->whereIn('unidad', ['l', 'L'])
            ->update([
                'cantidad' => DB::raw('cantidad * 1000'),
                'unidad' => 'ML'
            ]);

        // Estandarizar UD, GR, ML a Mayúsculas
        DB::table('alimentos')->where(DB::raw('LOWER(unidad)'), 'ud')->update(['unidad' => 'UD']);
        DB::table('alimentos')->where(DB::raw('LOWER(unidad)'), 'gr')->update(['unidad' => 'GR']);
        DB::table('alimentos')->where(DB::raw('LOWER(unidad)'), 'ml')->update(['unidad' => 'ML']);

        // 2. Modificar la columna para restringir valores (usando string por compatibilidad amplia, pero validado en app)
        // En una base de datos real podríamos usar ENUM, pero Laravel prefiere validación en capa de aplicación.
        // Aseguramos que la columna sea nullable o no según necesidad, aquí la mantenemos como estaba pero limpia.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No es fácil revertir una multiplicación por 1000 sin perder precisión original de unidades mayores,
        // pero por integridad, al menos permitimos que la columna acepte otros valores si fuera necesario.
    }
};
