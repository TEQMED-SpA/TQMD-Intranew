<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cliente_id')->nullable();
            $table->string('numero_ticket', 10);
            $table->string('cliente', 255);
            $table->string('nombre_apellido', 255);
            $table->string('telefono', 50);
            $table->string('cargo', 255);
            $table->string('email', 255)->nullable();
            $table->string('id_numero_equipo', 120)->nullable();
            $table->string('modelo_maquina', 255)->nullable();
            $table->text('falla_presentada');
            $table->enum('momento_falla', ['En preparación', 'En diálisis', 'En desinfección', 'Otras']);
            $table->string('momento_falla_otras', 255)->nullable();
            $table->text('acciones_realizadas')->nullable();
            $table->enum('estado', ['pendiente', 'en_proceso', 'completado'])->default('pendiente');
            $table->unsignedBigInteger('tecnico_asignado_id')->nullable();
            $table->dateTime('fecha_visita')->nullable();
            $table->unsignedBigInteger('llamado_id')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();

            $table->foreign('cliente_id')->references('id')->on('clientes')->nullOnDelete();
            $table->foreign('tecnico_asignado_id')->references('id')->on('users')->nullOnDelete();

            $table->unique('numero_ticket');
            $table->index('cliente_id');
            $table->index('tecnico_asignado_id');
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
