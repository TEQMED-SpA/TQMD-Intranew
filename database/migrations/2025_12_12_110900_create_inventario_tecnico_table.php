<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventario_tecnico', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tecnico_id');
            $table->unsignedBigInteger('repuesto_id');
            $table->unsignedInteger('cantidad');
            $table->unsignedBigInteger('solicitud_id')->nullable();
            $table->enum('estado', ['asignado', 'devuelto'])->default('asignado');
            $table->string('observacion', 255)->nullable();
            $table->timestamps();

            $table->foreign('tecnico_id')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('repuesto_id')->references('id')->on('repuestos')->restrictOnDelete();
            $table->foreign('solicitud_id')->references('id')->on('solicitudes')->nullOnDelete();

            $table->index('tecnico_id');
            $table->index('repuesto_id');
            $table->index('solicitud_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventario_tecnico');
    }
};
