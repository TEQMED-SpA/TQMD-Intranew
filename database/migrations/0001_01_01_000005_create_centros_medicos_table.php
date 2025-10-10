<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('centros_medicos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->integer('cod_cliente')->nullable();
            $table->integer('cod_centro_dialisis')->nullable();
            $table->string('centro_dialisis', 255)->nullable();
            $table->string('razon_social', 255)->nullable();
            $table->string('direccion', 255)->nullable();
            $table->string('region', 120)->nullable();
            $table->string('telefono', 30)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('centros_medicos');
    }
};
