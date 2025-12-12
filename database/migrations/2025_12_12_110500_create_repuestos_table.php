<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repuestos', function (Blueprint $table) {
            $table->id();
            $table->string('serie', 100);
            $table->string('nombre', 120);
            $table->string('modelo', 100);
            $table->string('marca', 100);
            $table->unsignedBigInteger('estado_id');
            $table->string('ubicacion', 120)->nullable();
            $table->text('descripcion')->nullable();
            $table->unsignedInteger('stock');
            $table->string('foto', 500)->nullable();
            $table->unsignedBigInteger('categoria_id');
            $table->unsignedBigInteger('usuario_id');
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('estado_id')->references('id')->on('estados_repuestos')->restrictOnDelete();
            $table->foreign('categoria_id')->references('id')->on('categorias_repuestos')->restrictOnDelete();
            $table->foreign('usuario_id')->references('id')->on('users')->restrictOnDelete();

            $table->unique('serie');
            $table->index('estado_id');
            $table->index('categoria_id');
            $table->index('usuario_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repuestos');
    }
};
