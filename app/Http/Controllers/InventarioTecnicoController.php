<?php

namespace App\Http\Controllers;

use App\Models\InventarioTecnico;
use App\Models\Repuesto;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventarioTecnicoController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $isAdminOrAuditor = $this->isAdminOrAuditor($user);

        $q = \App\Models\InventarioTecnico::query()
            ->with(['tecnico', 'repuesto'])
            // Si NO es admin/auditor, forzamos su propio técnico_id
            ->when(!$isAdminOrAuditor, fn($qq) => $qq->where('tecnico_id', $user->id))
            // Si es admin/auditor, puede filtrar por cualquier técnico
            ->when($isAdminOrAuditor && $request->filled('tecnico_id'), fn($qq) => $qq->where('tecnico_id', $request->integer('tecnico_id')))
            ->when($request->filled('repuesto_id'), fn($qq) => $qq->where('repuesto_id', $request->integer('repuesto_id')))
            ->when($request->filled('estado'),      fn($qq) => $qq->where('estado', $request->input('estado')))
            ->orderByDesc('id');

        $items = $q->paginate(25)->withQueryString();

        // Catálogos
        $tecnicos  = \App\Models\User::orderBy('name')->get(['id', 'name']);
        $repuestos = \App\Models\Repuesto::orderBy('nombre')->get(['id', 'nombre', 'serie', 'modelo', 'marca']);
        $estados   = collect([['v' => 'asignado', 't' => 'Asignado'], ['v' => 'devuelto', 't' => 'Devuelto']]);

        // Totales por técnico (sobre el query final, respetando la restricción)
        $totalesPorTecnico = (clone $q)->select('tecnico_id', \DB::raw('SUM(cantidad) as total'))
            ->groupBy('tecnico_id')->get()->keyBy('tecnico_id');

        return view('inventario_tecnico.index', compact('items', 'tecnicos', 'repuestos', 'estados', 'totalesPorTecnico', 'isAdminOrAuditor'));
    }


    /**
     * Verifica si el usuario es admin o auditor (para ver todo el inventario técnico).
     */
    private function isAdminOrAuditor($user): bool
    {
        if (!$user) return false;

        // 1) si hay isAdmin()
        if (method_exists($user, 'isAdmin') && $user->isAdmin()) return true;

        // 2) si usas roles con hasRole()
        if (method_exists($user, 'hasRole')) {
            if ($user->hasRole('admin') || $user->hasRole('auditor')) return true;
        }

        // 3) si usas privilegios con tienePrivilegio()
        if (method_exists($user, 'tienePrivilegio')) {
            if ($user->tienePrivilegio(['ver_inventario_tecnico_todos', 'auditar_inventario'])) return true;
        }

        // 4) fallback: revisa relación rol->privilegios si existe
        try {
            $privs = $user->rol?->privilegios?->pluck('nombre')->filter()->values()->all() ?? [];
            if (array_intersect($privs, ['ver_inventario_tecnico_todos', 'auditar_inventario', 'administrador'])) return true;
        } catch (\Throwable $e) {
            // ignorar
        }

        return false;
    }



    /**
     * Devolver (total o parcial) desde inventario del técnico hacia bodega.
     * - reduce cantidad del registro 'asignado'
     * - si queda en 0, marca 'devuelto'
     * - aumenta stock en repuestos
     * - opcional: inserta kardex en movimientos_stock
     */
    public function devolver(Request $request, \App\Models\InventarioTecnico $item)
    {
        $user = $request->user();
        $isAdminOrAuditor = $this->isAdminOrAuditor($user);

        // Si no es admin/auditor, solo puede devolver ítems propios
        if (!$isAdminOrAuditor && $item->tecnico_id !== $user->id) {
            return back()->with('error', 'No tienes permiso para operar inventario de otro técnico.');
        }

        $data = $request->validate([
            'cantidad'    => 'required|integer|min:1',
            'observacion' => 'nullable|string|max:255',
        ]);

        if ($item->estado !== 'asignado') {
            return back()->with('error', 'El ítem ya está marcado como devuelto.');
        }

        if ($data['cantidad'] > $item->cantidad) {
            return back()->with('error', "No puedes devolver más de {$item->cantidad} unidad(es).");
        }

        \DB::beginTransaction();
        try {
            // 1) actualizar inventario del técnico
            $nuevaCantidad = $item->cantidad - $data['cantidad'];
            $obs = trim(($item->observacion ? $item->observacion . '; ' : '') . ($request->observacion ?? 'Devolución a bodega'));
            $item->update([
                'cantidad'    => $nuevaCantidad,
                'estado'      => $nuevaCantidad <= 0 ? 'devuelto' : 'asignado',
                'observacion' => $obs,
            ]);

            // 2) aumentar stock del repuesto
            \DB::table('repuestos')->where('id', $item->repuesto_id)
                ->update(['stock' => \DB::raw("stock + {$data['cantidad']}"), 'updated_at' => now()]);

            // 3) kardex (opcional)
            try {
                if (\DB::getSchemaBuilder()->hasTable('movimientos_stock')) {
                    \DB::table('movimientos_stock')->insert([
                        'repuesto_id'      => $item->repuesto_id,
                        'tipo'             => 'entrada',
                        'cantidad'         => $data['cantidad'],
                        'centro_medico_id' => null, // bodega general; ajusta si manejas multibodega
                        'referencia_tipo'  => 'devolucion_tecnico',
                        'referencia_id'    => $item->id,
                        'fecha_hora'       => now(),
                        'created_at'       => now(),
                        'updated_at'       => now(),
                    ]);
                }
            } catch (\Throwable $e) {
                // no romper si la tabla/columnas difieren
            }

            \DB::commit();
            return back()->with('success', "Devueltas {$data['cantidad']} unidad(es) a bodega.");
        } catch (\Throwable $e) {
            \DB::rollBack();
            return back()->with('error', 'Error al devolver a bodega: ' . $e->getMessage());
        }
    }
}
