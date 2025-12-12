<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
use App\Models\Equipo;
use App\Models\Cliente;
use App\Models\CentroMedico;
use App\Models\TipoEquipo;
use Illuminate\Http\Request;

class EquipoController extends Controller
{
    public function index(Request $r)
    {
        $query = Equipo::with(['centro', 'centro.cliente', 'tipo'])
            ->when($r->filled('tipo_mantencion'), fn($q) => $q->where('tipo_mantencion', $r->input('tipo_mantencion')))
            ->when($r->filled('tipo_equipo_id'), fn($q) => $q->where('tipo_equipo_id', (int) $r->input('tipo_equipo_id')))
            ->when($r->filled('estado'), fn($q) => $q->where('estado', $r->input('estado')))
            ->when($r->filled('estado_mantencion'), function ($q) use ($r) {
                $hoy = now()->toDateString();

                return match ($r->input('estado_mantencion')) {
                    'vencida' => $q->whereDate('proxima_mantencion', '<', $hoy),
                    'proxima' => $q->whereBetween('proxima_mantencion', [$hoy, now()->addDays(30)->toDateString()]),
                    'aldia' => $q->whereDate('proxima_mantencion', '>', now()->addDays(30)->toDateString()),
                    default => $q,
                };
            })
            ->when(
                $r->filled('cliente_id'),
                fn($q) =>
                $q->whereHas('centro', fn($w) => $w->where('cliente_id', (int) $r->input('cliente_id')))
            )
            ->when(
                $r->filled('centro_medico_id'),
                fn($q) =>
                $q->where('centro_medico_id', (int) $r->input('centro_medico_id'))
            )
            ->when($r->filled('buscar'), function ($q) use ($r) {
                $b = '%' . $r->input('buscar') . '%';
                $q->where(function ($w) use ($b) {
                    $w->where('nombre', 'like', $b)
                        ->orWhere('modelo', 'like', $b)
                        ->orWhere('marca', 'like', $b)
                        ->orWhere('numero_serie', 'like', $b)
                        ->orWhere('id_maquina', 'like', $b)
                        ->orWhere('codigo', 'like', $b);
                });
            })
            ->orderByDesc('id');

        $equipos         = $query->paginate(20)->withQueryString();
        $clientes        = Cliente::orderBy('nombre')->get(['id', 'nombre']);
        $centros_medicos = CentroMedico::orderBy('nombre')->get(['id', 'nombre', 'cliente_id']);
        $tipos_equipo    = TipoEquipo::where('activo', true)->orderBy('nombre')->get(['id', 'nombre']);

        return view('equipos.index', compact('equipos', 'clientes', 'centros_medicos', 'tipos_equipo'));
    }

    public function show(Equipo $equipo)
    {
        $equipo->load(['centro', 'centro.cliente', 'tipo']);
        return view('equipos.show', compact('equipo'));
    }

    public function create()
    {
        $clientes        = Cliente::orderBy('nombre')->get(['id', 'nombre']);
        $centros_medicos = CentroMedico::orderBy('nombre')->get(['id', 'nombre', 'cliente_id']);
        $tipos_equipo    = TipoEquipo::where('activo', true)->orderBy('nombre')->get(['id', 'nombre']);
        return view('equipos.create', compact('clientes', 'centros_medicos', 'tipos_equipo'));
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'cliente_id'           => 'required|exists:clientes,id', // Solo para validar coherencia
            'centro_medico_id'     => 'required|exists:centros_medicos,id',
            'codigo'               => ['required', 'string', 'max:80', Rule::unique('equipos', 'codigo')],
            'tipo_equipo_id'       => ['nullable', 'exists:tipos_equipo,id'],
            'nombre'               => 'required|string|max:150',
            'modelo'               => 'nullable|string|max:100',
            'marca'                => 'nullable|string|max:100',
            'id_maquina'           => 'nullable|string|max:100',
            'numero_serie'         => 'nullable|string|max:120',
            'horas_uso'            => 'nullable|integer|min:0',
            'estado'               => ['nullable', Rule::in(['Operativo', 'En observacion', 'Fuera de servicio', 'Baja'])],
            'cant_dias_fuera_serv' => ['nullable', 'integer', 'min:0', 'max:365', 'required_if:estado,Fuera de servicio'],
            'descripcion'          => 'nullable|string',
            'ultima_mantencion'    => 'nullable|date',
            'proxima_mantencion'   => 'nullable|date|after_or_equal:ultima_mantencion',
            'tipo_mantencion'      => ['nullable', Rule::in(['T1', 'T2', 'T3', 'T4', 'Anual', 'Semestral', 'Trimestral', 'Ocasional', 'Otro'])],
        ], [
            'cant_dias_fuera_serv.required_if' => 'Indica cuántos días estará fuera de servicio.',
        ]);

        if (($data['estado'] ?? null) !== 'Fuera de servicio') {
            $data['cant_dias_fuera_serv'] = null;
        }

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
        $clientes = Cliente::orderBy('nombre')->get(['id', 'nombre']);
        $centros = CentroMedico::where('cliente_id', optional($equipo->centro)->cliente_id)
            ->orderBy('nombre')
            ->get();
        $tipos_equipo = TipoEquipo::where('activo', true)->orderBy('nombre')->get(['id', 'nombre']);

        return view('equipos.edit', compact('equipo', 'clientes', 'centros', 'tipos_equipo'));
    }

    public function update(Request $r, Equipo $equipo)
    {
        $data = $r->validate([
            'cliente_id'           => 'required|exists:clientes,id',
            'centro_medico_id'     => 'required|exists:centros_medicos,id',
            'codigo'               => ['required', 'string', 'max:80', Rule::unique('equipos', 'codigo')->ignore($equipo->id)],
            'tipo_equipo_id'       => ['nullable', 'exists:tipos_equipo,id'],
            'nombre'               => 'required|string|max:150',
            'modelo'               => 'nullable|string|max:100',
            'marca'                => 'nullable|string|max:100',
            'id_maquina'           => 'nullable|string|max:100',
            'numero_serie'         => 'nullable|string|max:120',
            'horas_uso'            => 'nullable|integer|min:0',
            'estado'               => ['nullable', Rule::in(['Operativo', 'En observacion', 'Fuera de servicio', 'Baja'])],
            'cant_dias_fuera_serv' => ['nullable', 'integer', 'min:0', 'max:365', 'required_if:estado,Fuera de servicio'],
            'descripcion'          => 'nullable|string',
            'ultima_mantencion'    => 'nullable|date',
            'proxima_mantencion'   => 'nullable|date|after_or_equal:ultima_mantencion',
            'tipo_mantencion'      => ['nullable', Rule::in(['T1', 'T2', 'T3', 'T4', 'Anual', 'Semestral', 'Trimestral', 'Ocasional', 'Otro'])],
        ], [
            'cant_dias_fuera_serv.required_if' => 'Indica cuántos días estará fuera de servicio.',
        ]);

        if (($data['estado'] ?? null) !== 'Fuera de servicio') {
            $data['cant_dias_fuera_serv'] = null;
        }

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
    public function porCentro(Request $request, CentroMedico $centro)
    {
        $query = $centro->equipos()->orderBy('nombre');

        if ($request->filled('tipo_equipo_id')) {
            $query->where('tipo_equipo_id', $request->integer('tipo_equipo_id'));
        }

        $equipos = $query
            ->get()
            ->map(function ($e) {
                $meta = $e->modelo ?: ($e->marca ?: ($e->numero_serie ?: $e->codigo));
                return [
                    'id' => $e->id,
                    'texto' => trim(($e->nombre ?? 'Equipo') . ($meta ? " — {$meta}" : '')),
                    'codigo' => $e->codigo,
                    'modelo' => $e->modelo,
                    'marca' => $e->marca,
                    'horas_uso' => $e->horas_uso,
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
