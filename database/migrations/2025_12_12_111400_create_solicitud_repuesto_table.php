<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitud_repuesto', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('solicitud_id');
            $table->unsignedBigInteger('repuesto_id');
            $table->integer('cantidad');
            $table->integer('orden')->nullable();
            $table->boolean('usado')->nullable();
            $table->enum('destino_devolucion', ['bodega', 'laboratorio', 'cliente', 'tecnico'])->nullable();
            $table->dateTime('fecha_uso')->nullable();
            $table->string('observacion', 250)->nullable();
            $table->timestamps();

            $table->foreign('solicitud_id')->references('id')->on('solicitudes')->restrictOnDelete();
            $table->foreign('repuesto_id')->references('id')->on('repuestos')->restrictOnDelete();

            $table->index('solicitud_id');
            $table->index('repuesto_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitud_repuesto');
    }
};
