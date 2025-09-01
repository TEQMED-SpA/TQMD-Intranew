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
        if ($request->filled('buscar')) {
            $query->buscar($request->buscar);
        }
        $centros = $query->orderBy('centro_dialisis')->paginate(15);
        return view('centros.index', compact('centros'));
    }

    public function create()
    {
        $clientes = Cliente::orderBy('nombre')->get();
        return view('centros.create', compact('clientes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'nullable|exists:clientes,id',
            'cod_cliente' => 'nullable|integer',
            'cod_centro_dialisis' => 'nullable|integer',
            'centro_dialisis' => 'required|string|max:255',
            'razon_social' => 'nullable|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20'
        ]);
        CentroMedico::create($request->all());
        return redirect()->route('centros.index')->with('success', 'Centro médico creado correctamente');
    }

    public function show(CentroMedico $centroMedico)
    {
        return view('centros.show', compact('centroMedico'));
    }

    public function edit(CentroMedico $centroMedico)
    {
        $clientes = Cliente::orderBy('nombre')->get();
        return view('centros.edit', compact('centroMedico', 'clientes'));
    }

    public function update(Request $request, CentroMedico $centroMedico)
    {
        $request->validate([
            'cliente_id' => 'nullable|exists:clientes,id',
            'cod_cliente' => 'nullable|integer',
            'cod_centro_dialisis' => 'nullable|integer',
            'centro_dialisis' => 'required|string|max:255',
            'razon_social' => 'nullable|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20'
        ]);
        $centroMedico->update($request->all());
        return redirect()->route('centros.show', $centroMedico)->with('success', 'Centro médico actualizado correctamente');
    }

    public function destroy(CentroMedico $centroMedico)
    {
        $centroMedico->delete();
        return redirect()->route('centros.index')->with('success', 'Centro médico eliminado correctamente');
    }
}
