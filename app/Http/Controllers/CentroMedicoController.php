<?php

namespace App\Http\Controllers;

use App\Models\CentroMedico;
use App\Models\Cliente;
use Illuminate\Http\Request;

class CentroMedicoController extends Controller
{
    /**
     * Listado general de centros médicos
     */
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

        $centros_medicos = $query
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();

        $clientes = Cliente::orderBy('nombre')->get(['id', 'nombre']);

        return view('centros_medicos.index', compact('centros_medicos', 'clientes'));
    }

    /**
     * Formulario de creación
     */
    public function create()
    {
        $clientes = Cliente::orderBy('nombre')->get(['id', 'nombre']);
        return view('centros_medicos.create', compact('clientes'));
    }

    /**
     * Guardar nuevo centro médico
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'cliente_id' => 'nullable|exists:clientes,id',
            'nombre'     => 'required|string|max:255',
            'direccion'  => 'nullable|string|max:255',
            'ciudad'     => 'nullable|string|max:120',
            'region'     => 'nullable|string|max:120',
            'telefono'   => 'nullable|string|max:30',
        ]);

        $clienteId  = $data['cliente_id'] ?? null;
        $codCliente = $clienteId ? (int) $clienteId : null;

        // Correlativo por cliente
        $siguienteCorrelativo = CentroMedico::when(
            $clienteId,
            fn($q) => $q->where('cliente_id', $clienteId)
        )->max('cod_centro_medico');

        $codCentro = (int) ($siguienteCorrelativo ?? 0) + 1;

        $payload = array_merge($data, [
            'cod_cliente'       => $codCliente,
            'cod_centro_medico' => $codCentro,
            'activo'            => 1,
        ]);

        CentroMedico::create($payload);

        return redirect()->route('centros_medicos.index')
            ->with('success', 'Centro médico creado correctamente');
    }

    /**
     * Mostrar un centro médico
     */
    public function show(CentroMedico $centroMedico)
    {
        $centroMedico->load(['cliente', 'equipos']);
        return view('centros_medicos.show', compact('centroMedico'));
    }

    /**
     * Formulario de edición
     */
    public function edit(CentroMedico $centroMedico)
    {
        $clientes = Cliente::orderBy('nombre')->get(['id', 'nombre']);
        return view('centros_medicos.edit', compact('centroMedico', 'clientes'));
    }

    /**
     * Actualizar centro médico
     */
    public function update(Request $request, CentroMedico $centroMedico)
    {
        $data = $request->validate([
            'cliente_id' => 'nullable|exists:clientes,id',
            'nombre'     => 'required|string|max:255',
            'direccion'  => 'nullable|string|max:255',
            'ciudad'     => 'nullable|string|max:120',
            'region'     => 'nullable|string|max:120',
            'telefono'   => 'nullable|string|max:30',
        ]);

        $centroMedico->update($data);

        return redirect()->route('centros_medicos.show', $centroMedico)
            ->with('success', 'Centro médico actualizado correctamente');
    }

    /**
     * Eliminar centro médico
     */
    public function destroy(CentroMedico $centroMedico)
    {
        $centroMedico->delete();

        return redirect()->route('centros_medicos.index')
            ->with('success', 'Centro médico eliminado correctamente');
    }

    /**
     * ==========================================
     *  CENTROS POR CLIENTE (para selects AJAX)
     *  GET /clientes/{cliente}/centros
     * ==========================================
     */
    public function porCliente($clienteId)
    {
        $centros = CentroMedico::where('cliente_id', $clienteId)
            ->orderBy('nombre')
            ->get()
            ->map(function ($c) {
                return [
                    'id'              => $c->id,
                    // El JS espera este nombre
                    'centro_dialisis' => $c->nombre,
                ];
            });

        return response()->json($centros);
    }
}
