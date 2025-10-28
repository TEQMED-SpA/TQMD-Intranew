<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CentroMedico;
use App\Models\Cliente;
use App\Models\Equipo;
use Illuminate\Http\Request;

class LookupController extends Controller
{
    public function centrosPorCliente(Request $request, Cliente $cliente)
    {
        $centros = CentroMedico::where('cliente_id', $cliente->id)
            ->orderBy('centro_dialisis')
            ->get(['id as id', 'centro_dialisis as nombre']);
        return response()->json($centros);
    }

    public function equiposPorCentro(Request $request, CentroMedico $centro)
    {
        $equipos = Equipo::where('centro_medico_id', $centro->id)
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'modelo', 'marca', 'numero_serie', 'id_maquina']);
        // Devuelve un texto amigable para mostrar en el <option>
        $equipos = $equipos->map(function ($e) {
            $meta = $e->modelo ?: ($e->marca ?: ($e->numero_serie ?: $e->id_maquina));
            return [
                'id'    => $e->id,
                'texto' => trim(($e->nombre ?? 'Equipo') . ' ' . ($meta ? "— {$meta}" : '')),
            ];
        });
        return response()->json($equipos);
    }
}
