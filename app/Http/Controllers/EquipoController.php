<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
use App\Models\Equipo;
use App\Models\Cliente;
use App\Models\CentroMedico;
use Illuminate\Http\Request;

class EquipoController extends Controller
{
    public function index(Request $r)
    {
        $query = Equipo::query();

        if ($r->filled('tipo_mantencion')) {
            $query->where('tipo_mantencion', $r->tipo_mantencion);
        }

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

        $q = Equipo::with(['centro', 'centro.cliente'])
            ->when(
                $r->filled('cliente_id'),
                fn($qq) =>
                $qq->whereHas('centro', fn($w) => $w->where('cliente_id', $r->integer('cliente_id')))
            )
            ->when(
                $r->filled('centro_medico_id'),
                fn($qq) =>
                $qq->where('centro_medico_id', $r->integer('centro_medico_id'))
            )
            ->when($r->filled('estado'), fn($qq) => $qq->where('estado', $r->input('estado')))
            ->when($r->filled('buscar'), function ($qq) use ($r) {
                $b = '%' . $r->input('buscar') . '%';
                $qq->where(function ($w) use ($b) {
                    $w->where('nombre', 'like', $b)
                        ->orWhere('modelo', 'like', $b)
                        ->orWhere('marca', 'like', $b)
                        ->orWhere('numero_serie', 'like', $b)
                        ->orWhere('id_maquina', 'like', $b)
                        ->orWhere('codigo', 'like', $b);
                });
            })
            ->orderByDesc('id');

        $equipos         = $q->paginate(20)->withQueryString();
        $clientes        = Cliente::orderBy('nombre')->get(['id', 'nombre']);
        $centros_medicos = CentroMedico::orderBy('nombre')->get(['id', 'nombre', 'cliente_id']);

        return view('equipos.index', compact('equipos', 'clientes', 'centros_medicos'));
    }

    public function show(Equipo $equipo)
    {
        $equipo->load(['centro', 'centro.cliente']);
        return view('equipos.show', compact('equipo'));
    }

    public function create()
    {
        $clientes        = Cliente::orderBy('nombre')->get(['id', 'nombre']);
        $centros_medicos = CentroMedico::orderBy('nombre')->get(['id', 'nombre', 'cliente_id']);
        return view('equipos.create', compact('clientes', 'centros_medicos'));
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'cliente_id'           => 'required|exists:clientes,id', // Solo para validar coherencia
            'centro_medico_id'     => 'required|exists:centros_medicos,id',
            'codigo'               => ['required', 'string', 'max:80', Rule::unique('equipos', 'codigo')],
            'nombre'               => 'required|string|max:150',
            'modelo'               => 'nullable|string|max:100',
            'marca'                => 'nullable|string|max:100',
            'id_maquina'           => 'nullable|string|max:100',
            'numero_serie'         => 'nullable|string|max:120',
            'horas_uso'            => 'nullable|integer|min:0',
            'estado'               => ['nullable', Rule::in(['Operativo', 'En observacion', 'Fuera de servicio', 'Baja'])],
            'cant_dias_fuera_serv' => 'nullable|integer|min:0|max:365',
            'descripcion'          => 'nullable|string',
            'ultima_mantencion'    => 'nullable|date',
            'proxima_mantencion'   => 'nullable|date|after_or_equal:ultima_mantencion',
            'tipo_mantencion'      => ['nullable', Rule::in(['T1', 'T2', 'T3'])],
        ]);

        $centro = CentroMedico::findOrFail($data['centro_medico_id']);
        if ((int) $centro->cliente_id !== (int) $data['cliente_id']) {
            return back()->withInput()->with('error', 'El centro seleccionado no pertenece al cliente.');
        }

        unset($data['cliente_id']); // no existe en la tabla equipos

        Equipo::create($data);

        return redirect()->route('equipos.index')->with('success', 'Equipo creado');
    }

    public function edit(Equipo $equipo)
    {
        $equipo->load(['centro', 'centro.cliente']);
        $clientes        = Cliente::orderBy('nombre')->get(['id', 'nombre']);
        $centros_medicos = CentroMedico::where('cliente_id', optional($equipo->centro)->cliente_id)
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        return view('equipos.edit', compact('equipo', 'clientes', 'centros_medicos'));
    }

    public function update(Request $r, Equipo $equipo)
    {
        $data = $r->validate([
            'cliente_id'           => 'required|exists:clientes,id',
            'centro_medico_id'     => 'required|exists:centros_medicos,id',
            'codigo'               => ['required', 'string', 'max:80', Rule::unique('equipos', 'codigo')->ignore($equipo->id)],
            'nombre'               => 'required|string|max:150',
            'modelo'               => 'nullable|string|max:100',
            'marca'                => 'nullable|string|max:100',
            'id_maquina'           => 'nullable|string|max:100',
            'numero_serie'         => 'nullable|string|max:120',
            'horas_uso'            => 'nullable|integer|min:0',
            'estado'               => ['nullable', Rule::in(['Operativo', 'En observacion', 'Fuera de servicio', 'Baja'])],
            'cant_dias_fuera_serv' => 'nullable|integer|min:0|max:365',
            'descripcion'          => 'nullable|string',
            'ultima_mantencion'    => 'nullable|date',
            'proxima_mantencion'   => 'nullable|date|after_or_equal:ultima_mantencion',
            'tipo_mantencion'      => ['nullable', Rule::in(['T1', 'T2', 'T3'])],
        ]);

        $centro = CentroMedico::findOrFail($data['centro_medico_id']);
        if ((int) $centro->cliente_id !== (int) $data['cliente_id']) {
            return back()->withInput()->with('error', 'El centro seleccionado no pertenece al cliente.');
        }

        unset($data['cliente_id']);

        $equipo->update($data);

        return redirect()->route('equipos.show', $equipo)->with('success', 'Equipo actualizado');
    }

    /**
     * ==========================================
     *  EQUIPOS POR CENTRO (para selects AJAX)
     *  GET /centros/{centro}/equipos
     * ==========================================
     */
    public function porCentro(CentroMedico $centro)
    {
        $equipos = $centro->equipos()
            ->orderBy('nombre')
            ->get()
            ->map(function ($e) {
                return [
                    'id'           => $e->id,
                    'nombre'       => $e->nombre,
                    'codigo'       => $e->codigo,
                    'modelo'       => $e->modelo,
                    'marca'        => $e->marca,
                    'horas_uso'    => $e->horas_uso,
                    'numero_serie' => $e->numero_serie,
                ];
            });

        return response()->json($equipos);
    }

    /**
     * Horas de uso (AJAX)
     * GET /equipos/{equipo}/horas-uso
     */
    public function horasUso(Equipo $equipo)
    {
        return response()->json([
            'horas_uso' => $equipo->horas_uso,
        ]);
    }

    public function destroy(Equipo $equipo)
    {
        $equipo->delete();
        return redirect()->route('equipos.index')->with('success', 'Equipo eliminado');
    }
}
