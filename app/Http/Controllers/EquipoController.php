<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Cliente;
use App\Models\CentroMedico;
use Illuminate\Http\Request;

class EquipoController extends Controller
{
    public function index(Request $r)
    {
        $query = \App\Models\Equipo::query();

        // Filtro por tipo de mantención
        if ($r->filled('tipo_mantencion')) {
            $query->where('tipo_mantencion', $r->tipo_mantencion);
        }

        // Filtro por estado de mantención
        if ($r->filled('estado_mantencion')) {
            $hoy = now()->toDateString();
            if ($r->estado_mantencion === 'vencida') {
                $query->whereDate('proxima_mantencion', '<', $hoy);
            } elseif ($r->estado_mantencion === 'proxima') {
                $query->whereBetween('proxima_mantencion', [$hoy, now()->addDays(30)->toDateString()]);
            } elseif ($r->estado_mantencion === 'aldia') {
                $query->whereDate('proxima_mantencion', '>', now()->addDays(30)->toDateString());
            }
        }

        $equipos = $query->orderBy('nombre')->paginate(15);

        $q = Equipo::with(['cliente', 'centro'])
            ->when($r->filled('cliente_id'), fn($qq) => $qq->where('cliente_id', $r->integer('cliente_id')))
            ->when($r->filled('centro_medico_id'), fn($qq) => $qq->where('centro_medico_id', $r->integer('centro_medico_id')))
            ->when($r->filled('estado'), fn($qq) => $qq->where('estado', $r->input('estado')))
            ->when($r->filled('buscar'), function ($qq) use ($r) {
                $b = '%' . $r->input('buscar') . '%';
                $qq->where(function ($w) use ($b) {
                    $w->where('nombre', 'like', $b)
                        ->orWhere('modelo', 'like', $b)
                        ->orWhere('marca', 'like', $b)
                        ->orWhere('numero_serie', 'like', $b)
                        ->orWhere('id_maquina', 'like', $b)
                        ->orWhere('sku', 'like', $b);
                });
            })
            ->orderByDesc('id');

        $equipos  = $q->paginate(20)->withQueryString();
        $clientes = Cliente::orderBy('nombre')->get(['id', 'nombre']);
        $centros_medicos  = CentroMedico::orderBy('nombre')->get(['id', 'nombre', 'cliente_id']);

        return view('equipos.index', compact('equipos', 'clientes', 'centros_medicos'));
    }

    public function show(Equipo $equipo)
    {
        $equipo->load(['cliente', 'centro']);
        return view('equipos.show', compact('equipo'));
    }

    public function create()
    {
        $clientes = Cliente::orderBy('nombre')->get(['id', 'nombre']);
        return view('equipos.create', compact('clientes'));
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'cliente_id'         => 'required|exists:clientes,id',
            'centro_medico_id'   => 'required|exists:centros_medicos,id',
            'nombre'             => 'required|string|max:150',
            'modelo'             => 'nullable|string|max:120',
            'marca'              => 'nullable|string|max:120',
            'id_maquina'         => 'nullable|string|max:100',
            'numero_serie'       => 'nullable|string|max:120',
            'horas_uso'          => 'nullable|integer|min:0',
            'estado'             => 'nullable|in:operativo,mantenimiento,baja',
            'sku'                => 'nullable|string|max:120',
            'descripcion'        => 'nullable|string',
            'ultima_mantencion'  => 'nullable|date',
            'proxima_mantencion' => 'nullable|date|after_or_equal:ultima_mantencion',
            'tipo_mantencion'    => 'nullable|in:T1,T2,T3,T4',
            'imagen'             => 'nullable|url|max:500',
        ]);

        // Consistencia: el centro debe pertenecer al cliente
        $centro = CentroMedico::findOrFail($data['centro_medico_id']);
        if ((int)$centro->cliente_id !== (int)$data['cliente_id']) {
            return back()->withInput()->with('error', 'El centro seleccionado no pertenece al cliente.');
        }

        Equipo::create($data);
        return redirect()->route('equipos.index')->with('success', 'Equipo creado');
    }

    public function edit(Equipo $equipo)
    {
        $clientes = Cliente::orderBy('nombre')->get(['id', 'nombre']);
        $centros_medicos  = CentroMedico::where('cliente_id', $equipo->cliente_id)->orderBy('nombre')->get(['id', 'nombre']);
        return view('equipos.edit', compact('equipo', 'clientes', 'centros_medicos'));
    }

    public function update(Request $r, Equipo $equipo)
    {
        $data = $r->validate([
            'cliente_id'         => 'required|exists:clientes,id',
            'centro_medico_id'   => 'required|exists:centros_medicos,id',
            'nombre'             => 'required|string|max:150',
            'modelo'             => 'nullable|string|max:120',
            'marca'              => 'nullable|string|max:120',
            'id_maquina'         => 'nullable|string|max:100',
            'numero_serie'       => 'nullable|string|max:120',
            'horas_uso'          => 'nullable|integer|min:0',
            'estado'             => 'nullable|in:operativo,mantenimiento,baja',
            'sku'                => 'nullable|string|max:120',
            'descripcion'        => 'nullable|string',
            'ultima_mantencion'  => 'nullable|date',
            'proxima_mantencion' => 'nullable|date|after_or_equal:ultima_mantencion',
            'tipo_mantencion'    => 'nullable|in:T1,T2,T3,T4',
            'imagen'             => 'nullable|url|max:500',
        ]);

        $centro = CentroMedico::findOrFail($data['centro_medico_id']);
        if ((int)$centro->cliente_id !== (int)$data['cliente_id']) {
            return back()->withInput()->with('error', 'El centro seleccionado no pertenece al cliente.');
        }

        $equipo->update($data);
        return redirect()->route('equipos.show', $equipo)->with('success', 'Equipo actualizado');
    }

    public function destroy(Equipo $equipo)
    {
        $equipo->delete();
        return redirect()->route('equipos.index')->with('success', 'Equipo eliminado');
    }
}
