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
            $table->string('numero_solicitud')->unique();
            $table->date('fecha_solicitud');
            $table->foreignId('tecnico_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('clinica_id')->constrained('centros_medicos')->cascadeOnDelete();
            $table->text('razon');
            $table->string('estado')->default('pendiente');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes');
    }
};
