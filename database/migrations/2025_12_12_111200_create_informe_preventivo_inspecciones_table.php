<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('informe_preventivo_inspecciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('informe_preventivo_id');
            $table->string('descripcion', 255);
            $table->enum('respuesta', ['SI', 'NO', 'N/A']);
            $table->string('comentario', 255)->nullable();
            $table->timestamps();

            $table->foreign('informe_preventivo_id')->references('id')->on('informes_preventivos')->restrictOnDelete();

            $table->index('informe_preventivo_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('informe_preventivo_inspecciones');
    }
};
