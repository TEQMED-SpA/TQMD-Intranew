<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('informes_preventivos', function (Blueprint $table) {
            if (Schema::hasColumn('informes_preventivos', 'numero_inventario')) {
                $table->dropColumn('numero_inventario');
            }

            if (! Schema::hasColumn('informes_preventivos', 'tipo_trabajo')) {
                $table->enum('tipo_trabajo', ['T1', 'T2', 'T3', 'T4', 'Anual', 'Semestral', 'Trimestral', 'Ocasional', 'Otro'])
                    ->default('T1')
                    ->after('equipo_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('informes_preventivos', function (Blueprint $table) {
            if (Schema::hasColumn('informes_preventivos', 'tipo_trabajo')) {
                $table->dropColumn('tipo_trabajo');
            }

            if (! Schema::hasColumn('informes_preventivos', 'numero_inventario')) {
                $table->string('numero_inventario')->nullable()->after('equipo_id');
            }
        });
    }
};
