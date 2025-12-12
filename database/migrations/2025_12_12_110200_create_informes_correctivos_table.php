<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('informes_correctivos', function (Blueprint $table) {
            $table->id();
            $table->string('numero_folio', 50);
            $table->unsignedBigInteger('cliente_id');
            $table->unsignedBigInteger('centro_medico_id');
            $table->unsignedBigInteger('equipo_id');
            $table->date('fecha_notificacion');
            $table->date('fecha_servicio');
            $table->text('problema_informado');
            $table->time('hora_inicio');
            $table->time('hora_cierre');
            $table->text('trabajo_realizado');
            $table->enum('condicion_equipo', ['operativo', 'en_observacion', 'fuera_de_servicio']);
            $table->unsignedBigInteger('usuario_id');
            $table->longText('firma');
            $table->longText('firma_cliente')->nullable();
            $table->string('firma_cliente_nombre', 150)->nullable();
            $table->timestamps();

            $table->foreign('cliente_id')->references('id')->on('clientes')->restrictOnDelete();
            $table->foreign('centro_medico_id')->references('id')->on('centros_medicos')->restrictOnDelete();
            $table->foreign('equipo_id')->references('id')->on('equipos')->restrictOnDelete();
            $table->foreign('usuario_id')->references('id')->on('users')->restrictOnDelete();

            $table->unique('numero_folio');
            $table->index('cliente_id');
            $table->index('centro_medico_id');
            $table->index('equipo_id');
            $table->index('usuario_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('informes_correctivos');
    }
};
