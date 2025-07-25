<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'id' => 1,
                'name' => 'Administrador Principal',
                'email' => null,
                'password' => '$2y$10$EPY9LSLOFLDDBriuJICmFOqmZdnDXxLJG8YFbog5LcExp77DBQvgC',
                'rol_id' => 1,
                'avatar' => 'Admin_41.png',
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Rafael Constanzo',
                'email' => 'rafael.constanzo@teqmed.cl',
                'password' => '$2y$10$taz0MePyPZGyzKB3zXmjh.1lponfrkEO2lBPCfO77Svgs1KWSqUbK',
                'rol_id' => 2,
                'avatar' => null,
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'name' => 'Nicolás Friz',
                'email' => 'nicolas.friz@teqmed.cl',
                'password' => '$2y$10$8D4u6AE4HilzTUx.5EI3Ee4qXwHALryE/rwRWZ74ymeV.j/QWRuJG',
                'rol_id' => 3,
                'avatar' => '',
                'estado' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'name' => 'Gabriela Sandoval',
                'email' => 'gabriela.sandoval@teqmed.cl',
                'password' => '$2y$10$GZjA7AferMPcbdAKmVCjOe9QnZmWX5we6BqsldFec2msy7oMcui0.',
                'rol_id' => 3,
                'avatar' => '',
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'name' => 'prueba funcionalidad',
                'email' => 'prueba@gmail.com',
                'password' => '$2y$10$ZCp6jqA8m70K5x/d.XVdqOBrPInVFZtf5E.hoIgxwv3CvtF7aHDVy',
                'rol_id' => 2,
                'avatar' => null,
                'estado' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 7,
                'name' => 'prueba funcionalidad',
                'email' => 'prueba1@gmail.com',
                'password' => '$2y$10$O8mbnJZPPiJEo1tUsmzbCuEyO9Iccja3ydB83W.1PSIPW7xlC8IBC',
                'rol_id' => 2,
                'avatar' => null,
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 8,
                'name' => 'Mauricio Medina',
                'email' => 'mauricio.medina@teqmed.cl',
                'password' => '$2y$10$ApZ1/h0sL0qidYCDpfBM.OwZj5p706j1e4ZQgkzWMH5kaaDOPH6he',
                'rol_id' => 2,
                'avatar' => null,
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 9,
                'name' => 'Recepción Teqmed',
                'email' => 'contacto@teqmed.cl',
                'password' => '$2y$10$jkAdHSefCvD7NDZ5u4TAFeJ2FKFUu0p/UKuQ.VJBa2xMJKaaW2LHq',
                'rol_id' => 2,
                'avatar' => null,
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 10,
                'name' => 'Jeannette Pino',
                'email' => 'jeannette.pino@teqmed.cl',
                'password' => '$2y$10$X3jOq/3hWH50VMuhGb76X.5YxqAFmgENvd1eO6P1HER2yF5tHBMxe',
                'rol_id' => 2,
                'avatar' => null,
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 11,
                'name' => 'Aleksandr Kapshukov',
                'email' => 'aleksandr.kapshukov@teqmed.cl',
                'password' => '$2y$10$93Cj0fMAX03dnzMGzXjJae.29K3lbTQa0/ceCjOpESr0JlZk8rTgi',
                'rol_id' => 2,
                'avatar' => null,
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
