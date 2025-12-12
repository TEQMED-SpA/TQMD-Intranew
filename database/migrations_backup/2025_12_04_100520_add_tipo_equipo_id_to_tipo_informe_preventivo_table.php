<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tipo_informe_preventivo', function (Blueprint $table) {
            $table->foreignId('tipo_equipo_id')
                ->nullable()
                ->after('nombre')
                ->constrained('tipos_equipo')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tipo_informe_preventivo', function (Blueprint $table) {
            $table->dropForeign(['tipo_equipo_id']);
            $table->dropColumn('tipo_equipo_id');
        });
    }
};
