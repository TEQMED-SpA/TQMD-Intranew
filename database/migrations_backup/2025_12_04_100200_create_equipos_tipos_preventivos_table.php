<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('equipos_tipos_preventivos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('equipo_id');
            $table->unsignedBigInteger('tipo_informe_preventivo_id');
            $table->timestamps();

            $table->unique(['equipo_id', 'tipo_informe_preventivo_id'], 'uq_equipo_tipo');

            $table->foreign('equipo_id')
                ->references('id')
                ->on('equipos')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreign('tipo_informe_preventivo_id')
                ->references('id')
                ->on('tipo_informe_preventivo')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipos_tipos_preventivos');
    }
};
