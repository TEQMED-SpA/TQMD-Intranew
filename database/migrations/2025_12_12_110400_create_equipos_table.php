<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150)->nullable();
            $table->string('id_maquina', 100)->nullable();
            $table->string('numero_serie', 120)->nullable();
            $table->unsignedInteger('horas_uso')->default(0);
            $table->boolean('activo')->default(true);
            $table->enum('estado', ['Operativo', 'En observacion', 'Fuera de servicio', 'Baja'])->nullable();
            $table->tinyInteger('cant_dias_fuera_serv')->nullable();
            $table->string('codigo', 80);
            $table->unsignedBigInteger('tipo_equipo_id')->nullable();
            $table->string('modelo', 100)->nullable();
            $table->string('marca', 100)->nullable();
            $table->string('serie', 120)->nullable();
            $table->unsignedBigInteger('centro_medico_id')->nullable();
            $table->text('descripcion')->nullable();
            $table->date('ultima_mantencion')->nullable();
            $table->date('proxima_mantencion')->nullable();
            $table->enum('tipo_mantencion', ['T1', 'T2', 'T3', 'T4', 'Anual', 'Semestral', 'Trimestral', 'Casual', 'Otro'])->nullable();
            $table->timestamps();

            $table->foreign('tipo_equipo_id')->references('id')->on('tipos_equipo')->nullOnDelete();
            $table->foreign('centro_medico_id')->references('id')->on('centros_medicos')->nullOnDelete();

            $table->unique('codigo');
            $table->index('tipo_equipo_id');
            $table->index('centro_medico_id');
            $table->index('numero_serie');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipos');
    }
};
