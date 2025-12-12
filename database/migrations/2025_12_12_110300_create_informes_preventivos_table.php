<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('informes_preventivos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tipo_informe_preventivo_id')->nullable();
            $table->string('numero_reporte_servicio', 50);
            $table->date('fecha');
            $table->unsignedBigInteger('centro_medico_id');
            $table->unsignedBigInteger('equipo_id');
            $table->enum('tipo_trabajo', ['T1', 'T2', 'T3', 'T4', 'Anual', 'Semestral', 'Trimestral', 'Casual', 'Otro'])->default('Otro');
            $table->enum('condicion_equipo', ['Operativo', 'En observacion', 'Fuera de servicio', 'Baja'])->default('Operativo');
            $table->unsignedInteger('horas_operacion')->default(0);
            $table->unsignedBigInteger('usuario_id');
            $table->text('comentarios')->nullable();
            $table->date('fecha_proximo_control')->nullable();
            $table->longText('firma_tecnico');
            $table->longText('firma_cliente')->nullable();
            $table->string('firma_cliente_nombre', 150)->nullable();
            $table->timestamps();

            $table->foreign('tipo_informe_preventivo_id')->references('id')->on('tipo_informe_preventivo')->nullOnDelete();
            $table->foreign('centro_medico_id')->references('id')->on('centros_medicos')->restrictOnDelete();
            $table->foreign('equipo_id')->references('id')->on('equipos')->restrictOnDelete();
            $table->foreign('usuario_id')->references('id')->on('users')->restrictOnDelete();

            $table->unique('numero_reporte_servicio');
            $table->index('centro_medico_id');
            $table->index('equipo_id');
            $table->index('usuario_id');
            $table->index('tipo_informe_preventivo_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('informes_preventivos');
    }
};
