<?php

namespace App\Http\Controllers;

use App\Models\CategoriaRepuesto;
use Illuminate\Http\Request;

class CategoriaRepuestoController extends Controller
{
    public function index(Request $request)
    {
        $categorias = CategoriaRepuesto::orderBy('nombre')->paginate(15);
        return view('categorias.index', compact('categorias'));
    }

    public function create()
    {
        return view('categorias.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:50',
            'subcategoria' => 'nullable|string|max:150',
        ]);
        CategoriaRepuesto::create($request->all());
        return redirect()->route('categorias.index')->with('success', 'Categoría creada correctamente');
    }

    public function show(CategoriaRepuesto $categoria)
    {
        return view('categorias.show', compact('categoria'));
    }

    public function edit(CategoriaRepuesto $categoria)
    {
        return view('categorias.edit', compact('categoria'));
    }

    public function update(Request $request, CategoriaRepuesto $categoria)
    {
        $request->validate([
            'nombre' => 'required|string|max:50',
            'subcategoria' => 'nullable|string|max:150',
        ]);
        $categoria->update($request->all());
        return redirect()->route('categorias.index')->with('success', 'Categoría actualizada correctamente');
    }

    public function destroy(CategoriaRepuesto $categoria)
    {
        $categoria->delete();
        return redirect()->route('categorias.index')->with('success', 'Categoría eliminada correctamente');
    }

    public function ajaxStore(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:50',
            'subcategoria' => 'nullable|string|max:150',
        ]);
        $categoria = CategoriaRepuesto::create([
            'nombre' => $request->nombre,
            'subcategoria' => $request->subcategoria,
        ]);
        return response()->json([
            'id' => $categoria->id,
            'nombre' => $categoria->nombre,
        ]);
    }
}
