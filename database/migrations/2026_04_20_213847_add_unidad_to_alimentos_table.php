<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('alimentos', function (Blueprint $table) {
            // Cambiamos cantidad a decimal para admitir 1.5 litros, por ejemplo
            $table->decimal('cantidad', 8, 2)->change();
            // Añadimos la unidad (por defecto 'ud') si no existe
            if (!Schema::hasColumn('alimentos', 'unidad')) {
                $table->string('unidad')->default('ud')->after('cantidad');
            }
        });
    }

    public function down()
    {
        Schema::table('alimentos', function (Blueprint $table) {
            $table->integer('cantidad')->change();
            $table->dropColumn('unidad');
        });
    }
};
