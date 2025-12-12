<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salidas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('solicitud_id')->nullable();
            $table->unsignedBigInteger('repuesto_id');
            $table->unsignedBigInteger('usuario_pedido_id');
            $table->unsignedBigInteger('usuario_requiere_id');
            $table->integer('cantidad');
            $table->unsignedBigInteger('centro_medico_id');
            $table->timestamp('fecha_hora')->useCurrent();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('solicitud_id')->references('id')->on('solicitudes')->nullOnDelete();
            $table->foreign('repuesto_id')->references('id')->on('repuestos')->restrictOnDelete();
            $table->foreign('usuario_pedido_id')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('usuario_requiere_id')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('centro_medico_id')->references('id')->on('centros_medicos')->restrictOnDelete();

            $table->index('solicitud_id');
            $table->index('repuesto_id');
            $table->index('usuario_pedido_id');
            $table->index('usuario_requiere_id');
            $table->index('centro_medico_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salidas');
    }
};
