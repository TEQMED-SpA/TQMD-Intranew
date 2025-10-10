<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 80)->unique();
            $table->timestamps();
        });

        DB::table('roles')->insert([
            ['nombre' => 'admin'],
            ['nombre' => 'tecnico'],
            ['nombre' => 'bodega'],
            ['nombre' => 'auditor'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
