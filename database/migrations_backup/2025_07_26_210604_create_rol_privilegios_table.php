<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rol_privilegios', function (Blueprint $table) {
            $table->foreignId('rol_id')->constrained('roles')->cascadeOnDelete();
            $table->foreignId('privilegio_id')->constrained('privilegios')->cascadeOnDelete();
            $table->primary(['rol_id', 'privilegio_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rol_privilegios');
    }
};
