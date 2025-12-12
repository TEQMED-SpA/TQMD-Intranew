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
            if (! Schema::hasColumn('informes_preventivos', 'condicion_equipo')) {
                $table->enum('condicion_equipo', ['Operativo', 'En observacion', 'Fuera de servicio', 'Baja'])
                    ->default('Operativo')
                    ->after('tipo_trabajo');
            }
        });
    }

    public function down(): void
    {
        Schema::table('informes_preventivos', function (Blueprint $table) {
            if (Schema::hasColumn('informes_preventivos', 'condicion_equipo')) {
                $table->dropColumn('condicion_equipo');
            }
        });
    }
};
