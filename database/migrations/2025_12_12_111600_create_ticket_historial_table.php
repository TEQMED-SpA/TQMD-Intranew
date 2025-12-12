<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_historial', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ticket_id');
            $table->string('usuario', 100)->nullable();
            $table->string('rol', 50)->nullable();
            $table->string('accion', 100);
            $table->string('estado_anterior', 50)->nullable();
            $table->string('estado_nuevo', 50)->nullable();
            $table->string('tecnico_anterior', 100)->nullable();
            $table->string('tecnico_nuevo', 100)->nullable();
            $table->text('comentario')->nullable();
            $table->string('foto', 255)->nullable();
            $table->dateTime('fecha')->useCurrent();

            $table->foreign('ticket_id')->references('id')->on('tickets')->restrictOnDelete();

            $table->index('ticket_id');
            $table->index('fecha');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_historial');
    }
};
