<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reg_repuestos_informes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('informe_correctivo_id')->nullable();
            $table->unsignedBigInteger('informe_preventivo_id')->nullable();
            $table->unsignedBigInteger('repuesto_id');
            $table->integer('cantidad');
            $table->timestamps();

            $table->foreign('informe_correctivo_id')->references('id')->on('informes_correctivos')->nullOnDelete();
            $table->foreign('informe_preventivo_id')->references('id')->on('informes_preventivos')->nullOnDelete();
            $table->foreign('repuesto_id')->references('id')->on('repuestos')->restrictOnDelete();

            $table->index('informe_correctivo_id');
            $table->index('informe_preventivo_id');
            $table->index('repuesto_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reg_repuestos_informes');
    }
};
