<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if table already exists and drop it if needed
        if (Schema::hasTable('llamados')) {
            Schema::dropIfExists('llamados');
        }

        Schema::create('llamados', function (Blueprint $table) {
            $table->id();
            $table->string('numero_llamado')->unique();
            $table->date('fecha_llamado');
            $table->time('hora_llamado');
            $table->foreignId('centro_medico_id')->constrained('centros_medicos')->onDelete('cascade');
            $table->string('nombre_informante');
            $table->string('id_equipo');
            $table->text('desperfecto');
            $table->foreignId('tecnico_asignado_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('categoria_llamado_id')->constrained('categoria_llamados')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('llamados');
    }
};
