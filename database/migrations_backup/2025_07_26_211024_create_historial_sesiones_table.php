<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historial_sesiones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_usuario')->constrained('users', 'id')->cascadeOnDelete();
            $table->dateTime('fecha_hora');
            $table->string('navegador', 255);
            $table->string('ip', 45);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historial_sesiones');
    }
};
