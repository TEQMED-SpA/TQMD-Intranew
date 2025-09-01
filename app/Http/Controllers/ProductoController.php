<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        $query = Producto::with('categoria');
        if ($request->filled('buscar')) {
            $query->buscar($request->buscar);
        }
        $productos = $query->orderBy('producto_nombre')->paginate(15);
        return view('productos.index', compact('productos'));
    }

    public function create()
    {
        $categorias = Categoria::orderBy('categoria_nombre')->get();
        return view('productos.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'producto_serie' => 'required|string|max:70',
            'producto_nombre' => 'required|string|max:70',
            'producto_modelo' => 'required|string|max:70',
            'producto_marca' => 'required|string|max:70',
            'producto_estado' => 'nullable|string|max:70',
            'producto_ubicacion' => 'required|string|max:70',
            'producto_descripcion' => 'nullable|string|max:70',
            'producto_stock' => 'required|integer|min:0',
            'producto_foto' => 'required|string|max:500',
            'categoria_id' => 'required|exists:categoria,categoria_id',
            'usuario_id' => 'required|exists:users,id'
        ]);
        Producto::create($request->all());
        return redirect()->route('productos.index')->with('success', 'Producto creado correctamente');
    }

    public function show(Producto $producto)
    {
        return view('productos.show', compact('producto'));
    }

    public function edit(Producto $producto)
    {
        $categorias = Categoria::orderBy('categoria_nombre')->get();
        return view('productos.edit', compact('producto', 'categorias'));
    }

    public function update(Request $request, Producto $producto)
    {
        $request->validate([
            'producto_serie' => 'required|string|max:70',
            'producto_nombre' => 'required|string|max:70',
            'producto_modelo' => 'required|string|max:70',
            'producto_marca' => 'required|string|max:70',
            'producto_estado' => 'nullable|string|max:70',
            'producto_ubicacion' => 'required|string|max:70',
            'producto_descripcion' => 'nullable|string|max:70',
            'producto_stock' => 'required|integer|min:0',
            'producto_foto' => 'required|string|max:500',
            'categoria_id' => 'required|exists:categoria,categoria_id',
            'usuario_id' => 'required|exists:users,id'
        ]);
        $producto->update($request->all());
        return redirect()->route('productos.show', $producto)->with('success', 'Producto actualizado correctamente');
    }

    public function destroy(Producto $producto)
    {
        $producto->delete();
        return redirect()->route('productos.index')->with('success', 'Producto eliminado correctamente');
    }
}
