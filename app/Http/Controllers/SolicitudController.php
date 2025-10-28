<?php

namespace App\Http\Controllers;

use App\Http\Requests\SolicitudRequest;
use App\Models\Solicitud;
use App\Models\Cliente;
use App\Models\CentroMedico;
use App\Models\Equipo;
use App\Models\EstadoSolicitud;
use App\Models\Repuesto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SolicitudController extends Controller
{
    public function index(Request $request)
    {
        $q = Solicitud::with(['clinica', 'equipo', 'tecnico'])
            ->when($request->filled('estado_id'), fn($qq) => $qq->where('estado_id', $request->integer('estado_id')))
            ->when($request->filled('cliente_id'), function ($qq) use ($request) {
                $qq->whereHas('clinica', fn($cqq) => $cqq->where('cliente_id', $request->integer('cliente_id')));
            })
            ->when($request->filled('clinica_id'), fn($qq) => $qq->where('clinica_id', $request->integer('clinica_id')))
            ->when($request->filled('equipo_id'),   fn($qq) => $qq->where('equipo_id',  $request->integer('equipo_id')))
            ->when($request->filled('numero'),      fn($qq) => $qq->where('numero_solicitud', 'like', '%' . $request->input('numero') . '%'))
            ->when($request->filled('desde'),       fn($qq) => $qq->whereDate('fecha_solicitud', '>=', $request->date('desde')))
            ->when($request->filled('hasta'),       fn($qq) => $qq->whereDate('fecha_solicitud', '<=', $request->date('hasta')))
            ->latest('id');

        $solicitudes = $q->paginate(20)->withQueryString();

        // Catálogos para filtros (ajusta los modelos si tienen otros nombres)
        $estados  = EstadoSolicitud::orderBy('nombre')->get(['id', 'nombre']);
        $clientes = Cliente::orderBy('nombre')->get(['id', 'nombre']);
        $centros_medicos  = CentroMedico::orderBy('nombre')->get(['id', 'nombre']);
        $equipos  = Equipo::orderBy('codigo')->get(['id', 'codigo', 'modelo']);

        return view('solicitudes.index', compact('solicitudes', 'estados', 'clientes', 'centros_medicos', 'equipos'));
    }

    public function create(Request $request)
    {
        $clientes  = Cliente::orderBy('nombre')->get(['id', 'nombre']);
        $centros_medicos   = collect(); // AJAX
        $equipos   = collect(); // AJAX
        $repuestos = Repuesto::where('stock', '>', 0)
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'serie', 'modelo', 'marca']); // 👈 columnas reales de tu BD
        $repuesto = null;
        if ($request->filled('repuesto_id')) {
            $repuesto = Repuesto::find((int)$request->input('repuesto_id'));
        }

        $prefillRepuestos = $repuesto ? [
            ['repuesto_id' => $repuesto->id, 'cantidad' => 1],
        ] : [];

        return view('solicitudes.create', [
            'clientes'         => $clientes,
            'centros_medicos'  => $centros_medicos,
            'equipos'          => $equipos,
            'repuestos'        => $repuestos,
            'repuesto'         => $repuesto,
            'prefillRepuestos' => $prefillRepuestos,
        ]);
    }

    public function store(SolicitudRequest $request)
    {
        // Consistencia cliente-centro-equipo
        $centro = CentroMedico::findOrFail($request->clinica_id);
        if ($centro->cliente_id != $request->cliente_id) {
            return back()->withInput()->with('error', 'El centro no pertenece al cliente seleccionado.');
        }
        $equipo = Equipo::findOrFail($request->equipo_id);
        if ($equipo->centro_medico_id != $centro->id) {
            return back()->withInput()->with('error', 'El equipo no pertenece al centro seleccionado.');
        }

        $request->validate([
            'cliente_id'       => 'required|exists:clientes,id',
            'clinica_id'       => 'required|exists:centros_medicos,id',
            'equipo_id'        => 'required|exists:equipos,id',
            'fecha_solicitud'  => 'required|date',
            'razon'            => 'required|string',
            'repuestos.*.repuesto_id' => 'required|exists:repuestos,id',
            'repuestos.*.cantidad'    => 'required|integer|min:1',
        ]);

        // Consistencias cruzadas
        $centro = \App\Models\CentroMedico::findOrFail($request->clinica_id);
        if ((int) $centro->cliente_id !== (int) $request->cliente_id) {
            return back()->withInput()->with('error', 'El centro no pertenece al cliente seleccionado.');
        }
        $equipo = \App\Models\Equipo::findOrFail($request->equipo_id);
        if ((int) $equipo->centro_medico_id !== (int) $request->clinica_id) {
            return back()->withInput()->with('error', 'El equipo no pertenece al centro seleccionado.');
        }

        DB::beginTransaction();
        try {
            $consecutivo = str_pad(
                Solicitud::whereYear('created_at', date('Y'))->count() + 1,
                4,
                '0',
                STR_PAD_LEFT
            );

            $solicitud = Solicitud::create([
                'numero_solicitud' => 'SOL-' . date('Ym') . '-' . $consecutivo,
                'fecha_solicitud'  => $request->fecha_solicitud,
                'tecnico_id'       => optional(auth()->user())->id,
                'clinica_id'       => $centro->id,
                'equipo_id'        => $equipo->id,
                'razon'            => $request->razon,
                'estado_id'        => 1, // pendiente
            ]);

            foreach ($request->repuestos as $item) {
                $solicitud->repuestos()->attach($item['repuesto_id'], [
                    'cantidad'            => (int) $item['cantidad'],
                    'observacion'         => $item['observacion'] ?? null,
                    // tracking de uso/devolución (pendiente al crear)
                    'usado'               => null, // null=pending, 1=usado, 0=no usado
                    'destino_devolucion'  => null, // 'bodega'|'laboratorio'|'cliente'
                    'fecha_uso'           => null,
                ]);
            }

            DB::commit();
            return redirect()->route('solicitudes.show', $solicitud)
                ->with('success', 'Solicitud creada correctamente');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Error al crear la solicitud: ' . $e->getMessage())->withInput();
        }
    }

    public function show(\App\Models\Solicitud $solicitud)
    {
        $solicitud->load([
            'clinica',
            'equipo',
            'tecnico',
            'repuestos' => function ($q) {
                $q->select('repuestos.*');
            }
        ]);

        return view('solicitudes.show', compact('solicitud'));
    }


    // JSON: centros por cliente
    public function centrosPorCliente(int $clienteId)
    {
        $centros_medicos = CentroMedico::where('cliente_id', $clienteId)
            ->orderBy('centro_dialisis')
            ->get(['id', 'centro_dialisis as nombre']);
        return response()->json($centros);
    }

    // JSON: equipos por centro
    public function equiposPorCentro(int $centroId)
    {
        $equipos = Equipo::where('centro_medico_id', $centroId)
            ->orderBy('codigo')
            ->get(['id', 'codigo', 'modelo']);
        return response()->json($equipos);
    }

    // Marcar uso/devolución de un ítem en la solicitud
    public function marcarUso(Request $request, \App\Models\Solicitud $solicitud, int $repuestoId)
    {
        $data = $request->validate([
            'usado'              => 'required|in:0,1,""', // permitimos "" (pendiente) desde el select
            'destino_devolucion' => 'nullable|in:bodega,laboratorio,cliente,tecnico',
        ]);

        // Normaliza valores
        $isPendiente = ($data['usado'] === '' || $data['usado'] === null);
        $usado = $isPendiente ? null : (int) $data['usado'];

        // El repuesto debe existir en el pivot de la solicitud
        $pivotRow = $solicitud->repuestos()->where('repuesto_id', $repuestoId)->first();
        if (!$pivotRow) {
            return back()->with('error', 'El repuesto no pertenece a esta solicitud.');
        }

        // Cantidad entregada (acumulada) para este repuesto en esta solicitud
        $entregado = (int) \DB::table('salidas')
            ->where('solicitud_id', $solicitud->id)
            ->where('repuesto_id', $repuestoId)
            ->sum('cantidad');

        // Si es "pendiente", solo actualiza pivot y salimos
        if (is_null($usado)) {
            $solicitud->repuestos()->updateExistingPivot($repuestoId, [
                'usado'              => null,
                'destino_devolucion' => null,
                'fecha_uso'          => null,
            ]);
            return back()->with('success', 'Estado de uso dejado en pendiente.');
        }

        // Si USADO = 1 => marcar usado y salir (no hay movimientos de stock adicionales)
        if ($usado === 1) {
            $solicitud->repuestos()->updateExistingPivot($repuestoId, [
                'usado'              => 1,
                'destino_devolucion' => null,
                'fecha_uso'          => now(),
            ]);
            return back()->with('success', 'Ítem marcado como USADO.');
        }

        // USADO = 0 => destino requerido
        if (empty($data['destino_devolucion'])) {
            return back()->with('error', 'Debes indicar el destino si el ítem NO fue usado.');
        }

        $destino = $data['destino_devolucion'];

        // Si no hubo entregas (salidas) previas y quieren devolver/prestar, avisar
        if ($entregado <= 0 && in_array($destino, ['bodega', 'laboratorio', 'tecnico'], true)) {
            // Para 'cliente' permitimos registrar igual (queda como no usado y en cliente, sin stock)
            if ($destino !== 'cliente') {
                return back()->with('error', 'Este ítem no tiene entregas registradas; no hay stock que devolver/asignar.');
            }
        }

        \DB::beginTransaction();
        try {
            // Actualiza pivot con NO USADO + destino
            $solicitud->repuestos()->updateExistingPivot($repuestoId, [
                'usado'              => 0,
                'destino_devolucion' => $destino,
                'fecha_uso'          => now(),
            ]);

            // Acciones según destino
            if ($destino === 'bodega' || $destino === 'laboratorio') {
                // Entrada a stock por lo ENTREGADO (total) hacia esa solicitud/repuesto
                if ($entregado > 0) {
                    \DB::table('repuestos')
                        ->where('id', $repuestoId)
                        ->update([
                            'stock' => \DB::raw("stock + {$entregado}"),
                            'updated_at' => now(),
                        ]);

                    // Kardex opcional
                    if (\DB::getSchemaBuilder()->hasTable('movimientos_stock')) {
                        \DB::table('movimientos_stock')->insert([
                            'repuesto_id'     => $repuestoId,
                            'tipo'            => 'entrada',
                            'cantidad'        => $entregado,
                            'centro_medico_id' => $solicitud->clinica_id,
                            'referencia_tipo' => 'devolucion_solicitud',
                            'referencia_id'   => $solicitud->id,
                            'fecha_hora'      => now(),
                            'created_at'      => now(),
                            'updated_at'      => now(),
                        ]);
                    }
                }
            } elseif ($destino === 'tecnico') {
                // Registrar "préstamo / tenencia" del técnico (no mueve stock porque ya salió en 'salidas')
                if ($entregado > 0) {
                    \DB::table('inventario_tecnico')->insert([
                        'tecnico_id'   => $solicitud->tecnico_id,
                        'repuesto_id'  => $repuestoId,
                        'cantidad'     => $entregado,
                        'solicitud_id' => $solicitud->id,
                        'estado'       => 'asignado',
                        'observacion'  => 'No usado - asignado a técnico desde solicitud',
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ]);
                }
            } else {
                // destino = cliente => solo registro en pivot, sin cambios de stock
            }

            \DB::commit();
            $msg = match ($destino) {
                'bodega'      => "No usado → devuelto a BODEGA (+{$entregado} a stock).",
                'laboratorio' => "No usado → enviado a LABORATORIO (+{$entregado} a stock).",
                'tecnico'     => "No usado → asignado al TÉCNICO (queda en su inventario).",
                default       => "No usado → queda en CLIENTE.",
            };
            return back()->with('success', $msg);
        } catch (\Throwable $e) {
            \DB::rollBack();
            return back()->with('error', 'Error al actualizar uso/devolución: ' . $e->getMessage());
        }
    }
}
