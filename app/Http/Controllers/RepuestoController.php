<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Http\Request;

class RepuestoController extends Controller
{
    public function index(Request $request)
    {
        $query = Producto::with('categoria');
        if ($request->filled('buscar')) {
            $query->buscar($request->buscar);
        }
        $repuestos = $query->orderBy('producto_nombre')->paginate(15);
        return view('repuestos.index', compact('repuestos'));
    }

    public function create()
    {
        $categorias = Categoria::orderBy('categoria_nombre')->get();
        // Extrae modelos, marcas y ubicaciones únicos de la tabla producto
        $modelos = Producto::select('producto_modelo')->distinct()->whereNotNull('producto_modelo')->pluck('producto_modelo');
        $marcas = Producto::select('producto_marca')->distinct()->whereNotNull('producto_marca')->pluck('producto_marca');
        $ubicaciones = Producto::select('producto_ubicacion')->distinct()->whereNotNull('producto_ubicacion')->pluck('producto_ubicacion');
        return view('repuestos.create', compact('categorias', 'modelos', 'marcas', 'ubicaciones'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'producto_nombre' => 'required|string|max:70',
            'producto_serie' => 'required|string|max:70',
            'producto_modelo' => 'required|string|max:70',
            'producto_marca' => 'required|string|max:70',
            'producto_estado' => 'nullable|string|max:70',
            'producto_ubicacion' => 'required|string|max:70',
            'producto_descripcion' => 'nullable|string|max:200',
            'producto_stock' => 'required|integer|min:0',
            'producto_foto' => 'nullable|file|image|max:2048',
            'categoria_id' => 'required|exists:categoria,categoria_id',
            'usuario_id' => 'required|exists:users,id'
        ]);
        $data = $request->all();
        if ($request->hasFile('producto_foto')) {
            $data['producto_foto'] = $request->file('producto_foto')->store('repuestos', 'public');
        }
        Producto::create($data);
        return redirect()->route('repuestos.index')->with('success', 'Repuesto creado correctamente');
    }

    public function show(Producto $producto)
    {
        return view('repuestos.show', ['repuesto' => $producto]);
    }

    public function edit(Producto $producto)
    {
        $categorias = Categoria::orderBy('categoria_nombre')->get();
        $modelos = Producto::select('producto_modelo')->distinct()->whereNotNull('producto_modelo')->pluck('producto_modelo');
        $marcas = Producto::select('producto_marca')->distinct()->whereNotNull('producto_marca')->pluck('producto_marca');
        $ubicaciones = Producto::select('producto_ubicacion')->distinct()->whereNotNull('producto_ubicacion')->pluck('producto_ubicacion');
        return view('repuestos.edit', [
            'repuesto' => $producto,
            'categorias' => $categorias,
            'modelos' => $modelos,
            'marcas' => $marcas,
            'ubicaciones' => $ubicaciones,
        ]);
    }

    public function update(Request $request, Producto $producto)
    {
        $request->validate([
            'producto_nombre' => 'required|string|max:70',
            'producto_serie' => 'required|string|max:70',
            'producto_modelo' => 'required|string|max:70',
            'producto_marca' => 'required|string|max:70',
            'producto_estado' => 'nullable|string|max:70',
            'producto_ubicacion' => 'required|string|max:70',
            'producto_descripcion' => 'nullable|string|max:200',
            'producto_stock' => 'required|integer|min:0',
            'producto_foto' => 'nullable|file|image|max:2048',
            'categoria_id' => 'required|exists:categoria,categoria_id',
            'usuario_id' => 'required|exists:users,id'
        ]);
        $data = $request->all();
        if ($request->hasFile('producto_foto')) {
            $data['producto_foto'] = $request->file('producto_foto')->store('repuestos', 'public');
        } else {
            unset($data['producto_foto']);
        }
        $producto->update($data);
        return redirect()->route('repuestos.show', $producto)->with('success', 'Repuesto actualizado correctamente');
    }

    public function destroy(Producto $producto)
    {
        $producto->delete();
        return redirect()->route('repuestos.index')->with('success', 'Repuesto eliminado correctamente');
    }
}
