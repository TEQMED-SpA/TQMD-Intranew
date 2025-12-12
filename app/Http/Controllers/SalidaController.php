<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
use App\Models\Repuesto;
use App\Models\Salida;
use App\Models\User;
use App\Models\CentroMedico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SalidaController extends Controller
{
    public function index(Request $request)
    {
        $query = Salida::with(['repuesto', 'usuarioPedido', 'usuarioRequiere', 'centroMedico'])
            ->orderByDesc('fecha_hora')
            ->when($request->filled('repuesto_id'), fn($q) => $q->where('repuesto_id', (int) $request->input('repuesto_id')))
            ->when($request->filled('tecnico_id'), fn($q) => $q->where('usuario_pedido_id', (int) $request->input('tecnico_id')))
            ->when($request->filled('centro_medico_id'), fn($q) => $q->where('centro_medico_id', (int) $request->input('centro_medico_id')))
            ->when($request->filled('desde'), fn($q) => $q->whereDate('fecha_hora', '>=', $request->input('desde')))
            ->when($request->filled('hasta'), fn($q) => $q->whereDate('fecha_hora', '<=', $request->input('hasta')));

        $salidas = $query->paginate(20)->withQueryString();

        $repuestos = Repuesto::orderBy('nombre')->get(['id', 'nombre', 'serie', 'modelo', 'marca']);
        $tecnicos = User::orderBy('name')->get(['id', 'name']);
        $centros = CentroMedico::orderBy('nombre')->get(['id', 'nombre']);

        return view('salidas.index', compact('salidas', 'repuestos', 'tecnicos', 'centros'));
    }

    /**
     * Entrega (aprobación) de un repuesto solicitado: crea 'salidas' y descuenta stock.
     */
    public function entregarItemDeSolicitud(Request $request, Solicitud $solicitud, Repuesto $repuesto)
    {
        $data = $request->validate([
            'cantidad' => 'required|integer|min:1',
        ]);

        // Verifica que el repuesto exista en el detalle de la solicitud (pivot)
        $pivot = $solicitud->repuestos()->where('repuesto_id', $repuesto->id)->first();
        if (!$pivot) {
            return back()->with('error', 'El repuesto no pertenece a esta solicitud.');
        }

        // Cantidad solicitada
        $solicitado = (int) $pivot->pivot->cantidad;

        // Ya entregado (acumulado en salidas)
        $yaEntregado = (int) DB::table('salidas')
            ->where('solicitud_id', $solicitud->id)
            ->where('repuesto_id', $repuesto->id)
            ->sum('cantidad');

        $restante = max(0, $solicitado - $yaEntregado);
        if ($restante <= 0) {
            return back()->with('error', 'Este repuesto ya fue entregado por completo.');
        }

        $aEntregar = (int) $data['cantidad'];
        if ($aEntregar > $restante) {
            return back()->with('error', "Cantidad excede lo restante por entregar ({$restante}).");
        }

        // Stock disponible
        $stockDisponible = (int) $repuesto->stock;
        if ($aEntregar > $stockDisponible) {
            return back()->with('error', "Stock insuficiente. Disponible: {$stockDisponible}.");
        }

        DB::beginTransaction();
        try {
            $salidaId = DB::table('salidas')->insertGetId([
                'solicitud_id'      => $solicitud->id,
                'repuesto_id'       => $repuesto->id,
                'usuario_pedido_id' => $solicitud->tecnico_id,          // quien pidió (técnico)
                'usuario_requiere_id' => Auth::id(),  // quien aprueba/entrega
                'cantidad'          => $aEntregar,
                'cantidad'          => $aEntregar,
                'centro_medico_id'  => $solicitud->clinica_id,
                'fecha_hora'        => now(),
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);

            // 2) Descontar stock del repuesto
            DB::table('repuestos')
                ->where('id', $repuesto->id)
                ->update(['stock' => DB::raw("GREATEST(stock - {$aEntregar}, 0)"), 'updated_at' => now()]);

            // 3) (Opcional) Kardex / movimientos_stock si existe
            try {
                if (DB::getSchemaBuilder()->hasTable('movimientos_stock')) {
                    DB::table('movimientos_stock')->insert([
                        'repuesto_id'     => $repuesto->id,
                        'tipo'            => 'salida',             // salida
                        'cantidad'        => $aEntregar,
                        'centro_medico_id' => $solicitud->clinica_id,
                        'referencia_tipo' => 'salida',
                        'referencia_id'   => $salidaId,
                        'fecha_hora'      => now(),
                        'created_at'      => now(),
                        'updated_at'      => now(),
                    ]);
                }
            } catch (\Throwable $e) {
                // si no existe la tabla o hay diferencia de columnas, no romper la entrega
            }

            // 4) (Opcional) si todo el repuesto quedó entregado, puedes actualizar estado global
            //    Ej.: estado_id = 2 (aprobada) cuando TODOS los ítems estén completos
            //    Aquí solo verificamos este repuesto; si quieres estado global, revisa todos los ítems.
            //    Lo dejamos a tu criterio de negocio.

            DB::commit();
            return back()->with('success', "Se entregaron {$aEntregar} unidad(es) de '{$repuesto->nombre}'. Restante ahora: " . ($restante - $aEntregar));
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Error al registrar la salida: ' . $e->getMessage());
        }
    }
}
