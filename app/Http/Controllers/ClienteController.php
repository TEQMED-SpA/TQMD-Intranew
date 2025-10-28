<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $query = Cliente::query();
        if ($request->filled('buscar')) {
            $query->buscar($request->buscar);
        }
        $clientes = $query->orderBy('nombre')->paginate(15);
        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'rut' => 'required|string|max:12|unique:clientes',
            'razon_social' => 'nullable|string|max:255',
        ]);
        Cliente::create($request->all());
        return redirect()->route('clientes.index')->with('success', 'Cliente creado correctamente');
    }

    public function show(\App\Models\Cliente $cliente)
    {
        $cliente->load([
            'centros_medicos' => fn($q) => $q->orderBy('nombre'),
        ]);

        return view('clientes.show', compact('cliente'));
    }

    public function edit(\App\Models\Cliente $cliente)
    {
        $cliente->load([
            'centros_medicos' => fn($q) => $q->orderBy('nombre'),
        ]);
        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'rut' => 'required|string|max:12|unique:clientes,rut,' . $cliente->id,
            'razon_social' => 'nullable|string|max:255',
        ]);
        $cliente->update($request->all());
        return redirect()->route('clientes.show', $cliente)->with('success', 'Cliente actualizado correctamente');
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();
        return redirect()->route('clientes.index')->with('success', 'Cliente eliminado correctamente');
    }
}
