<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimientos_stock', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('repuesto_id');
            $table->enum('tipo', ['entrada', 'salida']);
            $table->unsignedInteger('cantidad');
            $table->integer('stock_anterior');
            $table->integer('stock_nuevo');
            $table->unsignedBigInteger('usuario_id');
            $table->string('referencia_tipo', 40)->nullable();
            $table->unsignedBigInteger('referencia_id')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('repuesto_id')->references('id')->on('repuestos')->restrictOnDelete();
            $table->foreign('usuario_id')->references('id')->on('users')->restrictOnDelete();

            $table->index('repuesto_id');
            $table->index('usuario_id');
            $table->index(['referencia_tipo', 'referencia_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_stock');
    }
};
