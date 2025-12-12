<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('informe_correctivo_repuesto') && ! Schema::hasTable('reg_repuestos_informes')) {
            Schema::rename('informe_correctivo_repuesto', 'reg_repuestos_informes');
        }

        Schema::table('reg_repuestos_informes', function (Blueprint $table) {
            if (Schema::hasColumn('reg_repuestos_informes', 'informe_correctivo_id')) {
                $table->unsignedBigInteger('informe_correctivo_id')->nullable()->change();
            }

            if (! Schema::hasColumn('reg_repuestos_informes', 'informe_preventivo_id')) {
                $table->unsignedBigInteger('informe_preventivo_id')->nullable()->after('informe_correctivo_id');
            }

            if (Schema::hasColumn('reg_repuestos_informes', 'cantidad_usada')) {
                $table->renameColumn('cantidad_usada', 'cantidad');
            }
        });

        Schema::table('reg_repuestos_informes', function (Blueprint $table) {
            $table->foreign('informe_correctivo_id', 'fk_regrep_inf_corr')
                ->references('id')
                ->on('informes_correctivos')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreign('informe_preventivo_id', 'fk_regrep_inf_prev')
                ->references('id')
                ->on('informes_preventivos')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreign('repuesto_id', 'fk_regrep_repuesto')
                ->references('id')
                ->on('repuestos')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        // No se implementa rollback completo por simplicidad
    }
};
