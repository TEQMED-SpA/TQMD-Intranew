<?php

namespace App\Http\Controllers;

use App\Models\CentroMedico;
use App\Models\Cliente;
use Illuminate\Http\Request;

class CentroMedicoController extends Controller
{
    public function index(Request $request)
    {
        $query = CentroMedico::with('cliente');

        // Búsqueda opcional
        if ($request->filled('buscar')) {
            $texto = '%' . $request->input('buscar') . '%';
            $query->where(function ($w) use ($texto) {
                $w->where('nombre', 'like', $texto)
                    ->orWhere('ciudad', 'like', $texto)
                    ->orWhere('region', 'like', $texto)
                    ->orWhere('direccion', 'like', $texto)
                    ->orWhere('telefono', 'like', $texto);
            });
        }

        // Orden correcto por columna 'nombre'
        $centros_medicos = $query->orderBy('nombre')->paginate(15)->withQueryString();

        // (Opcional) para dropdowns, si tu index los usa
        $clientes = Cliente::orderBy('nombre')->get(['id', 'nombre']);

        return view('centros_medicos.index', compact('centros_medicos', 'clientes'));
    }

    public function create()
    {
        $clientes = Cliente::orderBy('nombre')->get(['id', 'nombre']);
        return view('centros_medicos.create', compact('clientes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'cliente_id' => 'nullable|exists:clientes,id',
            'nombre'     => 'required|string|max:255',
            'direccion'  => 'nullable|string|max:255',
            'ciudad'     => 'nullable|string|max:120',
            'region'     => 'nullable|string|max:120',
            'telefono'   => 'nullable|string|max:30',
            // NO validamos ni leemos cod_* ni activo desde el request
        ]);

        // === Generación automática ===
        $clienteId = $data['cliente_id'] ?? null;

        // cod_cliente: usamos el id del cliente (simple y único) o null si no hay cliente
        $codCliente = $clienteId ? (int)$clienteId : null;

        // cod_centro_medico: correlativo por cliente (o global si no hay cliente)
        $siguienteCorrelativo = \App\Models\CentroMedico::when($clienteId, fn($q) => $q->where('cliente_id', $clienteId))
            ->max('cod_centro_medico');
        $codCentro = (int)($siguienteCorrelativo ?? 0) + 1;

        $payload = array_merge($data, [
            'cod_cliente'        => $codCliente,
            'cod_centro_medico'  => $codCentro,
            'activo'             => 1, // siempre activo al crear
        ]);

        \App\Models\CentroMedico::create($payload);

        return redirect()->route('centros_medicos.index')
            ->with('success', 'Centro médico creado correctamente');
    }


    public function show(\App\Models\CentroMedico $centroMedico)
    {
        
        $centroMedico->load(['cliente', 'equipos']);
        return view('centros_medicos.show', compact('centroMedico'));
    }

    public function edit(CentroMedico $centroMedico)
    {
        $clientes = Cliente::orderBy('nombre')->get(['id', 'nombre']);
        return view('centros_medicos.edit', compact('centroMedico', 'clientes'));
    }

    public function update(Request $request, \App\Models\CentroMedico $centroMedico)
    {
        $data = $request->validate([
            'cliente_id' => 'nullable|exists:clientes,id',
            'nombre'     => 'required|string|max:255',
            'direccion'  => 'nullable|string|max:255',
            'ciudad'     => 'nullable|string|max:120',
            'region'     => 'nullable|string|max:120',
            'telefono'   => 'nullable|string|max:30',
        ]);

        // Mantener cod_* y activo tal como están en BD
        $centroMedico->update($data);

        return redirect()->route('centros_medicos.show', $centroMedico)
            ->with('success', 'Centro médico actualizado correctamente');
    }


    public function destroy(CentroMedico $centroMedico)
    {
        $centroMedico->delete();

        return redirect()->route('centros_medicos.index')
            ->with('success', 'Centro médico eliminado correctamente');
    }
}
