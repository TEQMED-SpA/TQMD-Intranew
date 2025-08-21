<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('producto', function (Blueprint $table) {
            $table->id('producto_id');
            $table->string('producto_serie', 70);
            $table->string('producto_nombre', 70);
            $table->string('producto_modelo', 70);
            $table->string('producto_marca', 70);
            $table->string('producto_estado', 70)->nullable();
            $table->string('producto_ubicacion', 70);
            $table->string('producto_descripcion', 70)->nullable();
            $table->integer('producto_stock');
            $table->string('producto_foto', 500);
            $table->foreignId('categoria_id')->constrained('categoria','categoria_id')->cascadeOnDelete();
            $table->foreignId('usuario_id')->constrained('users','id')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('producto');
    }
};