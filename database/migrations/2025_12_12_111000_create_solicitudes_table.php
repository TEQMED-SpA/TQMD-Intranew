<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitudes', function (Blueprint $table) {
            $table->id();
            $table->string('numero_solicitud', 80);
            $table->date('fecha_solicitud');
            $table->unsignedBigInteger('tecnico_id');
            $table->unsignedBigInteger('clinica_id');
            $table->unsignedBigInteger('equipo_id');
            $table->text('razon');
            $table->unsignedBigInteger('estado_id')->default(1);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('tecnico_id')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('clinica_id')->references('id')->on('centros_medicos')->restrictOnDelete();
            $table->foreign('equipo_id')->references('id')->on('equipos')->restrictOnDelete();
            $table->foreign('estado_id')->references('id')->on('estados_solicitudes')->restrictOnDelete();

            $table->unique('numero_solicitud');
            $table->index('tecnico_id');
            $table->index('clinica_id');
            $table->index('equipo_id');
            $table->index('estado_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes');
    }
};
