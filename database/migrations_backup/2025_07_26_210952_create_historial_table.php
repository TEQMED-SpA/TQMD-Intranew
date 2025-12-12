<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historial', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('users')->cascadeOnDelete();
            $table->string('nombre_tabla', 50);
            $table->string('accion', 20);
            $table->timestamp('fecha_hora')->useCurrent();
            $table->text('antes')->nullable();
            $table->text('despues')->nullable();
            $table->string('nombre_usuario', 40)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historial');
    }
};
