<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repuesto', function (Blueprint $table) {
            $table->id('repuesto_id');
            $table->string('repuesto_serie', 70);
            $table->string('repuesto_nombre', 70);
            $table->string('repuesto_modelo', 70);
            $table->string('repuesto_marca', 70);
            $table->string('repuesto_estado', 70)->nullable();
            $table->string('repuesto_ubicacion', 70);
            $table->string('repuesto_descripcion', 70)->nullable();
            $table->integer('repuesto_stock');
            $table->string('repuesto_foto', 500);
            $table->foreignId('categoria_id')->constrained('categoria', 'categoria_id')->cascadeOnDelete();
            $table->foreignId('usuario_id')->constrained('users', 'id')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repuesto');
    }
};
