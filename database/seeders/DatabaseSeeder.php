<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Ejecuta primero los roles para respetar la clave foránea en users
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
        ]);
    }
}
