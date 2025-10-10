<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('privilegios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 80)->unique();
            $table->timestamps();
        });

        // Seeds
        DB::table('privilegios')->insert([
            ['nombre'=>'ver_repuestos'],
            ['nombre'=>'editar_repuestos'],
            ['nombre'=>'ver_solicitudes'],
            ['nombre'=>'aprobar_solicitudes'],
            ['nombre'=>'ver_auditoria'],
        ]);

        Schema::create('rol_privilegios', function (Blueprint $table) {
            $table->foreignId('rol_id')->constrained('roles')->cascadeOnDelete();
            $table->foreignId('privilegio_id')->constrained('privilegios')->cascadeOnDelete();
            $table->primary(['rol_id', 'privilegio_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rol_privilegios');
        Schema::dropIfExists('privilegios');
    }
};