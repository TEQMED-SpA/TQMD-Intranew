<?php

namespace App\Http\Controllers;

use App\Models\Repuesto;
use App\Models\CategoriaRepuesto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RepuestoController extends Controller
{
    public function index(Request $request)
    {
        $query = Repuesto::query();

        if ($request->filled('buscar')) {
            $query->where(function ($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->buscar . '%')
                    ->orWhere('modelo', 'like', '%' . $request->buscar . '%')
                    ->orWhere('marca', 'like', '%' . $request->buscar . '%');
            });
        }

        if ($request->filled('categoria')) {
            $query->where('categoria_id', $request->categoria);
        }

        if ($request->filled('stock')) {
            switch ($request->stock) {
                case 'bajo':
                    $query->where('stock', '<', 10);
                    break;
                case 'medio':
                    $query->whereBetween('stock', [10, 50]);
                    break;
                case 'alto':
                    $query->where('stock', '>', 50);
                    break;
            }
        }

        $repuestos = $query->paginate(15);

        return view('repuestos.index', compact('repuestos'));
    }

    public function create()
    {
        $categorias = CategoriaRepuesto::orderBy('nombre')->get();
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
            'categoria_id' => 'required|exists:categorias_repuestos,id',
        ]);
        $data = $request->all();
        $data['usuario_id'] = Auth::id();

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('repuestos', 'public');
        }
        Repuesto::create($data);
        return redirect()->route('repuestos.index')->with('success', 'Repuesto creado correctamente');
    }

    public function show(Repuesto $repuesto)
    {
        $repuesto->load('categoria');
        return view('repuestos.show', compact('repuesto'));
    }

    public function edit(Repuesto $repuesto)
    {
        $categorias = CategoriaRepuesto::orderBy('nombre')->get();
        $modelos = Repuesto::select('modelo')->distinct()->whereNotNull('modelo')->pluck('modelo');
        $marcas = Repuesto::select('marca')->distinct()->whereNotNull('marca')->pluck('marca');
        $ubicaciones = Repuesto::select('ubicacion')->distinct()->whereNotNull('ubicacion')->pluck('ubicacion');
        return view('repuestos.edit', compact('repuesto', 'categorias', 'modelos', 'marcas', 'ubicaciones'));
    }

    public function update(Request $request, Repuesto $repuesto)
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
            'categoria_id' => 'required|exists:categorias_repuestos,id',
        ]);
        $data = $request->all();
        $data['usuario_id'] = $repuesto->usuario_id ?? Auth::id();

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('repuestos', 'public');
        } else {
            unset($data['foto']);
        }

        $repuesto->update($data);
        return redirect()->route('repuestos.index')->with('success', 'Repuesto actualizado correctamente');
    }

    public function destroy(Repuesto $repuesto)
    {
        $repuesto->update(['estado' => 'Inactivo']);
        return redirect()->route('repuestos.index')->with('success', 'Repuesto eliminado correctamente');
    }
}
