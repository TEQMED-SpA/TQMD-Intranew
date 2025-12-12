<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('informes_preventivos', function (Blueprint $table) {
            $table->unsignedBigInteger('tipo_informe_preventivo_id')
                ->nullable()
                ->after('id');

            $table->foreign('tipo_informe_preventivo_id')
                ->references('id')
                ->on('tipo_informe_preventivo')
                ->cascadeOnUpdate()
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('informes_preventivos', function (Blueprint $table) {
            $table->dropForeign(['tipo_informe_preventivo_id']);
            $table->dropColumn('tipo_informe_preventivo_id');
        });
    }
};
