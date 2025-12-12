<?php

namespace App\Http\Controllers;

use App\Models\CentroMedico;
use App\Models\Cliente;
use App\Models\TipoEquipo;
use Illuminate\Http\Request;

class CentroMedicoController extends Controller
{
    /**
     * Listado general de centros médicos
     */
    public function index(Request $request)
    {
        $query = CentroMedico::with('cliente');

        // Búsqueda opcional
        if ($request->filled('buscar')) {
            $texto = '%' . $request->input('buscar') . '%';

            $query->where(function ($w) use ($texto) {
                $w->where('nombre', 'like', $texto)
                    ->orWhere('ciudad', 'like', $texto)
                    ->orWhere('region', 'like', $texto)
                    ->orWhere('direccion', 'like', $texto)
                    ->orWhere('telefono', 'like', $texto);
            });
        }

        $centros_medicos = $query
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();

        $clientes = Cliente::orderBy('nombre')->get(['id', 'nombre']);

        return view('centros_medicos.index', [
            'centros_medicos' => $centros_medicos,
            'clientes' => $clientes,
        ]);
    }

    /**
     * Formulario de creación
     */
    public function create()
    {
        $clientes = Cliente::orderBy('nombre')->get(['id', 'nombre']);

        return view('centros_medicos.create', [
            'clientes' => $clientes,
        ]);
    }

    /**
     * Guardar nuevo centro médico
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'cliente_id' => 'nullable|exists:clientes,id',
            'nombre'     => 'required|string|max:255',
            'direccion'  => 'nullable|string|max:255',
            'ciudad'     => 'nullable|string|max:120',
            'region'     => 'nullable|string|max:120',
            'telefono'   => 'nullable|string|max:30',
        ]);

        $clienteId  = $data['cliente_id'] ?? null;
        $codCliente = $clienteId ? (int) $clienteId : null;

        // Correlativo por cliente
        $siguienteCorrelativo = CentroMedico::when(
            $clienteId,
            fn($q) => $q->where('cliente_id', $clienteId)
        )->max('cod_centro_medico');

        $codCentro = (int) ($siguienteCorrelativo ?? 0) + 1;

        $payload = array_merge($data, [
            'cod_cliente'       => $codCliente,
            'cod_centro_medico' => $codCentro,
            'activo'            => 1,
        ]);

        CentroMedico::create($payload);

        return redirect()->route('centros_medicos.index')
            ->with('success', 'Centro médico creado correctamente');
    }

    /**
     * Mostrar un centro médico
     */
    public function show(Request $request, CentroMedico $centros_medico)
    {
        $centros_medico->load('cliente');

        $equiposTotal = $centros_medico->equipos()->count();
        $solicitudesTotal = $centros_medico->solicitudes()->count();
        $solicitudesPendientes = $centros_medico->solicitudes()
            ->whereHas('estado', function ($estado) {
                $estado->whereRaw('LOWER(nombre) = ?', ['pendiente']);
            })->count();

        $equiposQuery = $centros_medico->equipos()
            ->with('tipo')
            ->orderBy('nombre');

        if ($request->filled('buscar')) {
            $texto = '%' . $request->input('buscar') . '%';
            $equiposQuery->where(function ($q) use ($texto) {
                $q->where('nombre', 'like', $texto)
                    ->orWhere('modelo', 'like', $texto)
                    ->orWhere('marca', 'like', $texto)
                    ->orWhere('numero_serie', 'like', $texto)
                    ->orWhere('codigo', 'like', $texto);
            });
        }

        if ($request->filled('estado')) {
            $equiposQuery->where('estado', $request->input('estado'));
        }

        if ($request->filled('tipo_equipo_id')) {
            $equiposQuery->where('tipo_equipo_id', (int) $request->input('tipo_equipo_id'));
        }

        $equipos = $equiposQuery->paginate(10)->withQueryString();

        $tipos_equipo = TipoEquipo::where('activo', true)
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        $estadoOpciones = [
            'Operativo',
            'En observacion',
            'Fuera de servicio',
            'Baja',
        ];

        return view('centros_medicos.show', [
            // Alias consistente para tus blades
            'centroMedico' => $centros_medico,
            'equipos' => $equipos,
            'tipos_equipo' => $tipos_equipo,
            'estadoOpciones' => $estadoOpciones,
            'equiposTotal' => $equiposTotal,
            'solicitudesTotal' => $solicitudesTotal,
            'solicitudesPendientes' => $solicitudesPendientes,
        ]);
    }

    /**
     * Formulario de edición
     */
    public function edit(CentroMedico $centros_medico)
    {
        $clientes = Cliente::orderBy('nombre')->get(['id', 'nombre']);

        return view('centros_medicos.edit', [
            // Alias consistente para tus blades
            'centroMedico' => $centros_medico,
            'clientes' => $clientes,
        ]);
    }

    /**
     * Actualizar centro médico
     */
    public function update(Request $request, CentroMedico $centros_medico)
    {
        $data = $request->validate([
            'cliente_id' => 'nullable|exists:clientes,id',
            'nombre'     => 'required|string|max:255',
            'direccion'  => 'nullable|string|max:255',
            'ciudad'     => 'nullable|string|max:120',
            'region'     => 'nullable|string|max:120',
            'telefono'   => 'nullable|string|max:30',
        ]);

        $centros_medico->update($data);

        return redirect()->route('centros_medicos.show', $centros_medico)
            ->with('success', 'Centro médico actualizado correctamente');
    }

    /**
     * Eliminar centro médico
     */
    public function destroy(CentroMedico $centros_medico)
    {
        $centros_medico->delete();

        return redirect()->route('centros_medicos.index')
            ->with('success', 'Centro médico eliminado correctamente');
    }

    /**
     * ==========================================
     *  CENTROS POR CLIENTE (para selects AJAX)
     *  GET /clientes/{cliente}/centros
     * ==========================================
     */
    public function porCliente($clienteId)
    {
        $centros = CentroMedico::where('cliente_id', $clienteId)
            ->orderBy('nombre')
            ->get()
            ->map(function ($c) {
                return [
                    'id'              => $c->id,
                    // El JS espera este nombre
                    'centro_dialisis' => $c->nombre,
                ];
            });

        return response()->json($centros);
    }
}
