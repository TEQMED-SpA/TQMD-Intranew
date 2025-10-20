<?php

namespace App\Http\Controllers;

use App\Models\Repuesto;
use App\Models\EstadoRepuesto;
use App\Models\CategoriaRepuesto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RepuestoController extends Controller
{
    public function index(Request $request)
    {
        $query = Repuesto::query();

        $bajaId = \App\Models\EstadoRepuesto::where('nombre', 'baja')->value('id');
        if ($bajaId) {
            $query->where('estado_id', '!=', $bajaId);
        }

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
        \Log::info('Entrando a RepuestoController@create', ['user_id' => auth()->id()]);
        $categorias = CategoriaRepuesto::orderBy('nombre')->get();
        $modelos     = Repuesto::select('modelo')->distinct()->whereNotNull('modelo')->pluck('modelo');
        $marcas      = Repuesto::select('marca')->distinct()->whereNotNull('marca')->pluck('marca');
        $ubicaciones = Repuesto::select('ubicacion')->distinct()->whereNotNull('ubicacion')->pluck('ubicacion');

        $estados = \App\Models\EstadoRepuesto::orderBy('nombre')->get(); // <—

        return view('repuestos.create', compact('categorias', 'modelos', 'marcas', 'ubicaciones', 'estados'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'       => 'required|string|max:70',
            'serie'        => 'required|string|max:70',
            'modelo'       => 'required|string|max:70',
            'marca'        => 'required|string|max:70',
            'ubicacion'    => 'required|string|max:70',
            'descripcion'  => 'nullable|string|max:200',
            'stock'        => 'required|integer|min:0',
            'foto'         => 'nullable|file|image|max:2048',
            'categoria_id' => 'required|exists:categorias_repuestos,id',
            'estado_id'    => 'required|exists:estados_repuestos,id', // <—
        ]);

        $data['usuario_id'] = \Auth::id();

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
        $modelos     = Repuesto::select('modelo')->distinct()->whereNotNull('modelo')->pluck('modelo');
        $marcas      = Repuesto::select('marca')->distinct()->whereNotNull('marca')->pluck('marca');
        $ubicaciones = Repuesto::select('ubicacion')->distinct()->whereNotNull('ubicacion')->pluck('ubicacion');
        $estados     = \App\Models\EstadoRepuesto::orderBy('nombre')->get(); // <—
        return view('repuestos.edit', compact('repuesto', 'categorias', 'modelos', 'marcas', 'ubicaciones', 'estados'));
    }

    public function update(Request $request, Repuesto $repuesto)
    {
        $data = $request->validate([
            'nombre'       => 'required|string|max:70',
            'serie'        => 'required|string|max:70',
            'modelo'       => 'required|string|max:70',
            'marca'        => 'required|string|max:70',
            'ubicacion'    => 'required|string|max:70',
            'descripcion'  => 'nullable|string|max:200',
            'stock'        => 'required|integer|min:0',
            'foto'         => 'nullable|file|image|max:2048',
            'categoria_id' => 'required|exists:categorias_repuestos,id',
            'estado_id'    => 'required|exists:estados_repuestos,id', // <—
        ]);

        $data['usuario_id'] = $repuesto->usuario_id ?? \Auth::id();

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
        $bajaId = \App\Models\EstadoRepuesto::where('nombre', 'baja')->value('id');
        if ($bajaId) {
            $repuesto->update(['estado_id' => $bajaId]);
        }
        return redirect()->route('repuestos.index')->with('success', 'Repuesto marcado como baja.');
    }

    public function baja()
    {
        $bajaId = \App\Models\EstadoRepuesto::where('nombre', 'baja')->value('id');

        $repuestos = \App\Models\Repuesto::with(['categoria', 'estado'])
            ->where('estado_id', $bajaId)
            ->paginate(15);

        return view('repuestos.baja', compact('repuestos'));
    }
}
