<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salidas', function (Blueprint $table) {
            $table->id('salida_id');
            $table->foreignId('solicitud_id')->nullable()->constrained('solicitudes')->nullOnDelete();
            $table->foreignId('producto_id')->constrained('producto', 'producto_id')->cascadeOnDelete();
            $table->foreignId('usuario_pedido_id')->constrained('users', 'id')->cascadeOnDelete();
            $table->foreignId('usuario_requiere_id')->constrained('users', 'id')->cascadeOnDelete();
            $table->integer('cantidad');
            $table->foreignId('centro_medico_id')->constrained('centros_medicos')->cascadeOnDelete();
            $table->timestamp('fecha_hora')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salidas');
    }
};
