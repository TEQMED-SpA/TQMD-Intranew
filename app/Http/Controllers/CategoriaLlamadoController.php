<?php

namespace App\Http\Controllers;

use App\Models\CategoriaLlamado;
use Illuminate\Http\Request;

class CategoriaLlamadoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categorias = CategoriaLlamado::all();
        return view('categoria_llamados.index', compact('categorias'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('categoria_llamados.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|unique:categoria_llamados,nombre',
            'descripcion' => 'nullable',
        ]);

        $categoria = CategoriaLlamado::create($request->all());

        if ($request->ajax()) {
            return response()->json(['success' => true, 'categoria' => $categoria]);
        }

        return redirect()->route('categoria_llamados.index')
            ->with('success', 'Categoría de llamado creada exitosamente.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CategoriaLlamado  $categoriaLlamado
     * @return \Illuminate\Http\Response
     */
    public function show(CategoriaLlamado $categoriaLlamado)
    {
        return view('categoria_llamados.show', compact('categoriaLlamado'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CategoriaLlamado  $categoriaLlamado
     * @return \Illuminate\Http\Response
     */
    public function edit(CategoriaLlamado $categoriaLlamado)
    {
        return view('categoria_llamados.edit', compact('categoriaLlamado'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CategoriaLlamado  $categoriaLlamado
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CategoriaLlamado $categoriaLlamado)
    {
        $request->validate([
            'nombre' => 'required|unique:categoria_llamados,nombre,' . $categoriaLlamado->id,
            'descripcion' => 'nullable',
        ]);

        $categoriaLlamado->update($request->all());

        return redirect()->route('categoria_llamados.index')
            ->with('success', 'Categoría de llamado actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CategoriaLlamado  $categoriaLlamado
     * @return \Illuminate\Http\Response
     */
    public function destroy(CategoriaLlamado $categoriaLlamado)
    {
        $categoriaLlamado->delete();

        return redirect()->route('categoria_llamados.index')
            ->with('success', 'Categoría de llamado eliminada exitosamente.');
    }

    /**
     * Get all categories.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAllCategorias()
    {
        $categorias = CategoriaLlamado::all();
        return response()->json($categorias);
    }
}
