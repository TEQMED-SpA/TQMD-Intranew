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
        Schema::table('informes_correctivos', function (Blueprint $table) {
            $table->string('firma_cliente_nombre')->nullable()->after('firma_cliente');
        });

        Schema::table('informes_preventivos', function (Blueprint $table) {
            $table->string('firma_cliente_nombre')->nullable()->after('firma_cliente');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('informes_correctivos', function (Blueprint $table) {
            $table->dropColumn('firma_cliente_nombre');
        });

        Schema::table('informes_preventivos', function (Blueprint $table) {
            $table->dropColumn('firma_cliente_nombre');
        });
    }
};
