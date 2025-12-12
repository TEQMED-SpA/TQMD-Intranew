<?php

namespace Database\Seeders;

use App\Models\TipoEquipo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TipoEquipoSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            ['nombre' => 'Hemodiálisis', 'descripcion' => 'Dializadores y bombas de sangre'],
            ['nombre' => 'Aspirador de Secreciones', 'descripcion' => 'Equipos de aspiración médica'],
            ['nombre' => 'Autoclave', 'descripcion' => 'Esterilizadores a vapor'],
            ['nombre' => 'Balanzas', 'descripcion' => 'Balanzas clínicas y pediátricas'],
            ['nombre' => 'DEA', 'descripcion' => 'Desfibriladores externos automatizados'],
            ['nombre' => 'Planta de tratamiento de agua', 'descripcion' => 'Osmosis inversa / potabilización'],
        ];

        foreach ($tipos as $tipo) {
            TipoEquipo::updateOrCreate(
                ['slug' => Str::slug($tipo['nombre'])],
                [
                    'nombre' => $tipo['nombre'],
                    'descripcion' => $tipo['descripcion'],
                    'activo' => true,
                ]
            );
        }
    }
}
