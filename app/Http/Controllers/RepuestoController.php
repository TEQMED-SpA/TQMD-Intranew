<?php

namespace App\Http\Controllers;

use App\Models\Repuesto;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RepuestoController extends Controller
{
    public function index(Request $request)
    {
        $query = Repuesto::with('categoria');
        if ($request->filled('buscar')) {
            $query->buscar($request->buscar);
        }
        $repuestos = $query->orderBy('nombre')->paginate(15);
        return view('repuestos.index', compact('repuestos'));
    }

    public function create()
    {
        $categorias = Categoria::orderBy('categoria_nombre')->get();
        // Extrae modelos, marcas y ubicaciones únicos de la tabla Repuesto
        $modelos = Repuesto::select('modelo')->distinct()->whereNotNull('modelo')->pluck('modelo');
        $marcas = Repuesto::select('marca')->distinct()->whereNotNull('marca')->pluck('marca');
        $ubicaciones = Repuesto::select('ubicacion')->distinct()->whereNotNull('ubicacion')->pluck('ubicacion');
        return view('repuestos.create', compact('categorias', 'modelos', 'marcas', 'ubicaciones'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:70',
            'serie' => 'required|string|max:70',
            'modelo' => 'required|string|max:70',
            'marca' => 'required|string|max:70',
            'estado' => 'nullable|string|max:70',
            'ubicacion' => 'required|string|max:70',
            'descripcion' => 'nullable|string|max:200',
            'stock' => 'required|integer|min:0',
            'foto' => 'nullable|file|image|max:2048',
            'categoria_id' => 'required|exists:categoria,categoria_id',
        ]);
        $data = $request->all();
        $data['usuario_id'] = auth()->id();

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('repuestos', 'public');
        }
        Repuesto::create($data);
        return redirect()->route('repuestos.index')->with('success', 'Repuesto creado correctamente');
    }

    public function show(Repuesto $Repuesto)
    {
        return view('repuestos.show', ['repuesto' => $Repuesto]);
    }

    public function edit(Repuesto $Repuesto)
    {
        $categorias = Categoria::orderBy('categoria_nombre')->get();
        $modelos = Repuesto::select('modelo')->distinct()->whereNotNull('modelo')->pluck('modelo');
        $marcas = Repuesto::select('marca')->distinct()->whereNotNull('marca')->pluck('marca');
        $ubicaciones = Repuesto::select('ubicacion')->distinct()->whereNotNull('ubicacion')->pluck('ubicacion');
        return view('repuestos.edit', [
            'repuesto' => $Repuesto,
            'categorias' => $categorias,
            'modelos' => $modelos,
            'marcas' => $marcas,
            'ubicaciones' => $ubicaciones,
        ]);
    }

    public function update(Request $request, Repuesto $Repuesto)
    {
        $request->validate([
            'nombre' => 'required|string|max:70',
            'serie' => 'required|string|max:70',
            'modelo' => 'required|string|max:70',
            'marca' => 'required|string|max:70',
            'estado' => 'nullable|string|max:70',
            'ubicacion' => 'required|string|max:70',
            'descripcion' => 'nullable|string|max:200',
            'stock' => 'required|integer|min:0',
            'foto' => 'nullable|file|image|max:2048',
            'categoria_id' => 'required|exists:categoria,categoria_id',
        ]);
        $data = $request->all();
        $data['usuario_id'] = $producto->usuario_id ?? auth()->id();
        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('repuestos', 'public');
        } else {
            unset($data['foto']);
        }
        $Repuesto->update($data);
        return redirect()->route('repuestos.index')->with('success', 'Repuesto actualizado correctamente');
    }

    public function destroy(Repuesto $Repuesto)
    {
        $Repuesto->delete();
        return redirect()->route('repuestos.index')->with('success', 'Repuesto eliminado correctamente');
    }
}
