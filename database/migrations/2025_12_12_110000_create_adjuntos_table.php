<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('adjuntos', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type', 50);
            $table->unsignedBigInteger('entity_id');
            $table->string('ruta', 500);
            $table->string('nombre', 200)->nullable();
            $table->string('mime', 120)->nullable();
            $table->bigInteger('tamano')->nullable();
            $table->unsignedBigInteger('subido_por')->nullable();
            $table->timestamp('created_at')->useCurrent();
            
            $table->foreign('subido_por')->references('id')->on('users')->nullOnDelete();
            $table->index(['entity_type', 'entity_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adjuntos');
    }
};
