<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('informe_preventivo_inspecciones', function (Blueprint $table) {
            if (! Schema::hasColumn('informe_preventivo_inspecciones', 'comentario')) {
                $table->string('comentario', 255)
                    ->nullable()
                    ->after('respuesta');
            }
        });
    }

    public function down(): void
    {
        Schema::table('informe_preventivo_inspecciones', function (Blueprint $table) {
            if (Schema::hasColumn('informe_preventivo_inspecciones', 'comentario')) {
                $table->dropColumn('comentario');
            }
        });
    }
};
