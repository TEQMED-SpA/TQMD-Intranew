<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\User;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('privilegio:ver_auditorias');
    }

    public function index(Request $request)
    {
        $query = Audit::with('user');

        // Filtros
        if ($request->filled('entity_type')) {
            $query->where('entity_type', $request->entity_type);
        }

        if ($request->filled('entity_id')) {
            $query->where('entity_id', $request->entity_id);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $query->orderBy('created_at', 'desc');

        $audits = $query->paginate(15);

        // Datos para filtros
        $entityTypes = Audit::select('entity_type')
            ->distinct()
            ->pluck('entity_type');

        $actions = Audit::select('action')
            ->distinct()
            ->pluck('action');

        return view('audits.index', compact('audits', 'entityTypes', 'actions'));
    }

    public function show(Audit $audit)
    {
        $audit->load('user');
        return view('audits.show', compact('audit'));
    }

    public function entityHistory($entityType, $entityId)
    {
        $audits = Audit::forEntity($entityType, $entityId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('audits.entity-history', compact('audits', 'entityType', 'entityId'));
    }

    public function userActivity($userId)
    {
        $audits = Audit::byUser($userId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $user = User::findOrFail($userId);

        return view('audits.user-activity', compact('audits', 'user'));
    }

    public function export(Request $request)
    {
        $this->middleware('privilegio:exportar_auditorias');

        $query = Audit::with('user');
        // Aplicar los mismos filtros que en el método index
        // ... (código de filtros)

        $audits = $query->get();

        return Excel::download(new AuditsExport($audits), 'audit-log.xlsx');
    }
}
