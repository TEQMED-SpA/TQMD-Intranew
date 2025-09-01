<?php

namespace App\Http\Controllers;

use App\Models\Privilegio;
use Illuminate\Http\Request;

class PrivilegioController extends Controller
{
    public function index()
    {
        $privilegios = Privilegio::orderBy('nombre')->paginate(15);
        return view('privilegios.index', compact('privilegios'));
    }

    public function create()
    {
        return view('privilegios.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:50|unique:privilegios',
        ]);
        Privilegio::create($request->all());
        return redirect()->route('privilegios.index')->with('success', 'Privilegio creado correctamente');
    }

    public function show(Privilegio $privilegio)
    {
        return view('privilegios.show', compact('privilegio'));
    }

    public function edit(Privilegio $privilegio)
    {
        return view('privilegios.edit', compact('privilegio'));
    }

    public function update(Request $request, Privilegio $privilegio)
    {
        $request->validate([
            'nombre' => 'required|string|max:50|unique:privilegios,nombre,' . $privilegio->id,
        ]);
        $privilegio->update($request->all());
        return redirect()->route('privilegios.show', $privilegio)->with('success', 'Privilegio actualizado correctamente');
    }

    public function destroy(Privilegio $privilegio)
    {
        $privilegio->delete();
        return redirect()->route('privilegios.index')->with('success', 'Privilegio eliminado correctamente');
    }
}
