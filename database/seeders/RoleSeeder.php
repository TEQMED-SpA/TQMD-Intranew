<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insert([
            ['id' => 1, 'nombre' => 'Administrador', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'nombre' => 'Usuario', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'nombre' => 'Supervisor', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
