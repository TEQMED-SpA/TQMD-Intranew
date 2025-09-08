<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function index(Request $request)
    {
        $categorias = Categoria::orderBy('categoria_nombre')->paginate(15);
        return view('categorias.index', compact('categorias'));
    }

    public function create()
    {
        return view('categorias.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'categoria_nombre' => 'required|string|max:50',
            'categoria_subcategoria' => 'nullable|string|max:150',
        ]);
        Categoria::create($request->all());
        return redirect()->route('categorias.index')->with('success', 'Categoría creada correctamente');
    }

    public function show(Categoria $categoria)
    {
        return view('categorias.show', compact('categoria'));
    }

    public function edit(Categoria $categoria)
    {
        return view('categorias.edit', compact('categoria'));
    }

    public function update(Request $request, Categoria $categoria)
    {
        $request->validate([
            'categoria_nombre' => 'required|string|max:50',
            'categoria_subcategoria' => 'nullable|string|max:150',
        ]);
        $categoria->update($request->all());
        return redirect()->route('categorias.show', $categoria)->with('success', 'Categoría actualizada correctamente');
    }

    public function destroy(Categoria $categoria)
    {
        $categoria->delete();
        return redirect()->route('categorias.index')->with('success', 'Categoría eliminada correctamente');
    }
    
    public function ajaxStore(Request $request)
    {
        $request->validate([
            'categoria_nombre' => 'required|string|max:50',
            'categoria_subcategoria' => 'nullable|string|max:150',
        ]);
        $categoria = Categoria::create([
            'categoria_nombre' => $request->categoria_nombre,
            'categoria_subcategoria' => $request->categoria_subcategoria,
        ]);
        return response()->json([
            'id' => $categoria->categoria_id,
            'nombre' => $categoria->categoria_nombre,
        ]);
    }
}
