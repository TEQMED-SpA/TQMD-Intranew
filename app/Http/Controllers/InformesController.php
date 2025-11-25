<?php

namespace App\Http\Controllers;

use App\Models\CentroMedico;
use App\Models\Cliente;
use App\Models\Equipo;
use App\Models\InformeCorrectivo;
use App\Models\InformePreventivo;
use App\Models\Repuesto;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class InformesController extends Controller
{
    // =========================================================================
    // INDEX: listado con tabs + filtros
    // =========================================================================

    public function index(Request $request): View
    {
        $search         = $request->input('search');
        $clienteId      = $request->input('cliente_id');
        $centroMedicoId = $request->input('centro_medico_id');
        $activeTab      = $request->input('tab', 'correctivos');
        $sortCorrectivos = $request->input('sort_correctivos');
        $dirCorrectivos  = $request->input('dir_correctivos', 'desc');
        $sortPreventivos = $request->input('sort_preventivos');
        $dirPreventivos  = $request->input('dir_preventivos', 'desc');

        $dirCorrectivos  = strtolower($dirCorrectivos) === 'asc' ? 'asc' : 'desc';
        $dirPreventivos  = strtolower($dirPreventivos) === 'asc' ? 'asc' : 'desc';

        // Base queries
        $correctivosQuery = InformeCorrectivo::with(['cliente', 'centroMedico', 'equipo', 'usuario']);

        $preventivosQuery = InformePreventivo::with(['centroMedico', 'equipo', 'usuario']);

        $sortableCorrectivos = [
            'numero_folio'    => 'numero_folio',
            'fecha_servicio'  => 'fecha_servicio',
            'cliente'         => Cliente::select('nombre')
                ->whereColumn('clientes.id', 'informes_correctivos.cliente_id'),
            'centro'          => CentroMedico::select('centro_dialisis')
                ->whereColumn('centros_medicos.id', 'informes_correctivos.centro_medico_id'),
            'equipo'          => Equipo::select('modelo')
                ->whereColumn('equipos.id', 'informes_correctivos.equipo_id'),
            'tecnico'         => User::select('name')
                ->whereColumn('users.id', 'informes_correctivos.usuario_id'),
            'condicion_equipo' => 'condicion_equipo',
        ];

        if ($sortCorrectivos && isset($sortableCorrectivos[$sortCorrectivos])) {
            $correctivosQuery->orderBy($sortableCorrectivos[$sortCorrectivos], $dirCorrectivos);
        } else {
            $correctivosQuery->latest('fecha_servicio');
        }

        $sortablePreventivos = [
            'numero_reporte_servicio' => 'numero_reporte_servicio',
            'fecha'                   => 'fecha',
            'centro'                  => CentroMedico::select('centro_dialisis')
                ->whereColumn('centros_medicos.id', 'informes_preventivos.centro_medico_id'),
            'equipo'                  => Equipo::select('modelo')
                ->whereColumn('equipos.id', 'informes_preventivos.equipo_id'),
            'tecnico'                 => User::select('name')
                ->whereColumn('users.id', 'informes_preventivos.usuario_id'),
            'fecha_proximo_control'   => 'fecha_proximo_control',
        ];

        if ($sortPreventivos && isset($sortablePreventivos[$sortPreventivos])) {
            $preventivosQuery->orderBy($sortablePreventivos[$sortPreventivos], $dirPreventivos);
        } else {
            $preventivosQuery->latest('fecha');
        }

        // Filtros compartidos
        if ($clienteId) {
            $correctivosQuery->where('cliente_id', $clienteId);
        }

        if ($centroMedicoId) {
            $correctivosQuery->where('centro_medico_id', $centroMedicoId);
            $preventivosQuery->where('centro_medico_id', $centroMedicoId);
        }

        if ($search) {
            $correctivosQuery->where(function ($q) use ($search) {
                $q->where('numero_folio', 'like', "%{$search}%")
                    ->orWhereHas('cliente', function ($q2) use ($search) {
                        $q2->where('nombre', 'like', "%{$search}%");
                    })
                    ->orWhereHas('centroMedico', function ($q2) use ($search) {
                        $q2->where('centro_dialisis', 'like', "%{$search}%");
                    })
                    ->orWhereHas('equipo', function ($q2) use ($search) {
                        $q2->where('codigo', 'like', "%{$search}%")
                            ->orWhere('modelo', 'like', "%{$search}%");
                    });
            });

            $preventivosQuery->where(function ($q) use ($search) {
                $q->where('numero_reporte_servicio', 'like', "%{$search}%")
                    ->orWhereHas('centroMedico', function ($q2) use ($search) {
                        $q2->where('centro_dialisis', 'like', "%{$search}%");
                    })
                    ->orWhereHas('equipo', function ($q2) use ($search) {
                        $q2->where('modelo', 'like', "%{$search}%")
                            ->orWhere('serie', 'like', "%{$search}%");
                    });
            });
        }

        // Paginación independiente
        $correctivosAppends = [
            'tab'              => 'correctivos',
            'search'           => $search,
            'cliente_id'       => $clienteId,
            'centro_medico_id' => $centroMedicoId,
        ];

        if ($sortCorrectivos) {
            $correctivosAppends['sort_correctivos'] = $sortCorrectivos;
            $correctivosAppends['dir_correctivos']  = $dirCorrectivos;
        }

        $correctivos = $correctivosQuery
            ->paginate(10, ['*'], 'page_correctivos')
            ->appends($correctivosAppends);

        $preventivosAppends = [
            'tab'              => 'preventivos',
            'search'           => $search,
            'cliente_id'       => $clienteId,
            'centro_medico_id' => $centroMedicoId,
        ];

        if ($sortPreventivos) {
            $preventivosAppends['sort_preventivos'] = $sortPreventivos;
            $preventivosAppends['dir_preventivos']  = $dirPreventivos;
        }

        $preventivos = $preventivosQuery
            ->paginate(10, ['*'], 'page_preventivos')
            ->appends($preventivosAppends);

        return view('informes.index', [
            'correctivos'    => $correctivos,
            'preventivos'    => $preventivos,
            'clientes'       => Cliente::orderBy('nombre')->get(),
            'centrosMedicos' => CentroMedico::orderBy('centro_dialisis')->get(),
            'filters'        => [
                'search'           => $search,
                'cliente_id'       => $clienteId,
                'centro_medico_id' => $centroMedicoId,
                'tab'              => $activeTab,
            ],
            'sortCorrectivos' => $sortCorrectivos,
            'dirCorrectivos'  => $sortCorrectivos ? $dirCorrectivos : null,
            'sortPreventivos' => $sortPreventivos,
            'dirPreventivos'  => $sortPreventivos ? $dirPreventivos : null,
        ]);
    }

    // =========================================================================
    // HELPERS
    // =========================================================================

    /**
     * Genera el número de reporte preventivo con las iniciales del usuario.
     * Ej: usuario "Nicolás Friz" → "NF0001", "NF0002", etc.
     */
    protected function generarNumeroPreventivo(): string
    {
        $user = Auth::user();
        $name = trim($user?->name ?? 'ASC');

        // Tomar máximo 2 iniciales del nombre
        $words     = preg_split('/\s+/', $name);
        $iniciales = '';

        foreach ($words as $word) {
            if ($word === '') {
                continue;
            }

            $iniciales .= mb_strtoupper(mb_substr($word, 0, 1));

            if (mb_strlen($iniciales) >= 2) {
                break;
            }
        }

        if ($iniciales === '') {
            $iniciales = 'ASC';
        }

        // Buscar el último número que empiece con esas iniciales
        $ultimoNumero = InformePreventivo::where('numero_reporte_servicio', 'LIKE', $iniciales . '%')
            ->max('numero_reporte_servicio');

        if ($ultimoNumero) {
            // Ej: "NF0004" → tomar la parte numérica después de las iniciales
            $correlativo = (int) substr($ultimoNumero, mb_strlen($iniciales)) + 1;
        } else {
            $correlativo = 1;
        }

        return $iniciales . str_pad($correlativo, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Retorna el logo en base64 para usar en PDFs o null si no existe.
     */
    protected function getLogoBase64(): ?string
    {
        try {
            $logoPath = public_path('images/logo.png');

            if (! file_exists($logoPath)) {
                Log::warning('Logo para PDF no encontrado en: ' . $logoPath);
                return null;
            }

            $logoType = pathinfo($logoPath, PATHINFO_EXTENSION);
            $logoData = file_get_contents($logoPath);

            return 'data:image/' . $logoType . ';base64,' . base64_encode($logoData);
        } catch (\Throwable $e) {
            Log::error('Error generando base64 del logo: ' . $e->getMessage());
            return null;
        }
    }

    // =========================================================================
    // CREATE: generador de informes (tabs + firmas)
    // =========================================================================

    public function create(): View
    {
        $centrosMedicos  = CentroMedico::orderBy('centro_dialisis')->get();
        $equipos         = Equipo::orderBy('nombre')->get();
        $repuestos       = Repuesto::orderBy('nombre')->get();
        $clientes        = Cliente::orderBy('nombre')->get();
        $siguienteNumero = $this->generarNumeroPreventivo();

        return view('informes.create', compact(
            'centrosMedicos',
            'equipos',
            'repuestos',
            'clientes',
            'siguienteNumero'
        ));
    }

    /**
     * Alias si tienes una ruta específica para crear preventivo.
     */
    public function createPreventivo(): View
    {
        return $this->create();
    }

    // =========================================================================
    // INFORME CORRECTIVO
    // =========================================================================

    public function showCorrectivo(int $id): View
    {
        $informe = InformeCorrectivo::with([
            'cliente',
            'centroMedico',
            'equipo',
            'usuario',
            'repuestos',
        ])->findOrFail($id);

        return view('informes.show-correctivo', compact('informe'));
    }

    public function storeCorrectivo(Request $request): Response|RedirectResponse
    {
        $request->validate([
            'centro_medico_id'    => 'required|exists:centros_medicos,id',
            'equipo_id'           => 'required|exists:equipos,id',
            'repuestos'           => 'nullable|array',
            'cantidades'          => 'nullable|array',
            'fecha_servicio'      => 'required|date',
            'fecha_notificacion'  => 'required|date|before:fecha_servicio',
            'problema_informado'  => 'required|string',
            'hora_inicio'         => 'required|date_format:H:i',
            'hora_cierre'         => 'required|date_format:H:i|after:hora_inicio',
            'trabajo_realizado'   => 'required|string',
            'condicion_equipo'    => 'required|in:operativo,en_observacion,fuera_de_servicio',
            'cliente_id'          => 'required|exists:clientes,id',
            'firma'               => 'required|string',
            'firma_cliente'       => 'nullable|string',
            'horas_uso'           => 'required|integer|min:0',
        ], [
            'hora_cierre.after'         => 'La hora de cierre debe ser posterior a la hora de inicio.',
            'fecha_notificacion.before' => 'La fecha de notificación debe ser anterior a la fecha de servicio.',
            'firma.required'            => 'La firma digital es obligatoria.',
            'horas_uso.required'        => 'Las horas de uso son obligatorias.',
            'horas_uso.integer'         => 'Horas de uso debe ser un número entero.',
        ]);

        // Validar horas de uso
        $equipo = Equipo::find($request->equipo_id);
        if ($equipo && $request->horas_uso < $equipo->horas_uso) {
            return back()
                ->withErrors([
                    'horas_uso' => 'Las horas de uso deben ser mayores o iguales a las horas actuales del equipo (' . $equipo->horas_uso . ').',
                ])
                ->withInput();
        }

        $informe = null;

        DB::transaction(function () use ($request, &$informe) {
            // Folio correlativo
            $ultimo_folio = InformeCorrectivo::max('numero_folio') ?? 0;
            $numero_folio = str_pad((int) $ultimo_folio + 1, 6, '0', STR_PAD_LEFT);

            $informe = InformeCorrectivo::create([
                'numero_folio'       => $numero_folio,
                'centro_medico_id'   => $request->centro_medico_id,
                'equipo_id'          => $request->equipo_id,
                'cliente_id'         => $request->cliente_id,
                'fecha_servicio'     => $request->fecha_servicio,
                'fecha_notificacion' => $request->fecha_notificacion,
                'problema_informado' => $request->problema_informado,
                'hora_inicio'        => $request->hora_inicio,
                'hora_cierre'        => $request->hora_cierre,
                'trabajo_realizado'  => $request->trabajo_realizado,
                'condicion_equipo'   => $request->condicion_equipo,
                'usuario_id'         => Auth::id(),
                'firma'              => $request->firma,
                'firma_cliente'      => $request->firma_cliente,
            ]);

            // Actualizar horas de uso
            $equipo = Equipo::find($request->equipo_id);
            if ($equipo) {
                $equipo->update(['horas_uso' => $request->horas_uso]);
            }

            // Repuestos + stock
            if ($request->repuestos && is_array($request->repuestos)) {
                foreach ($request->repuestos as $index => $repuestoId) {
                    $cantidad = $request->cantidades[$index] ?? 0;

                    if ($cantidad > 0) {
                        $repuesto = Repuesto::find($repuestoId);

                        if ($repuesto && $repuesto->stock >= $cantidad) {
                            $informe->repuestos()->attach($repuestoId, [
                                'cantidad_usada' => $cantidad,
                            ]);
                            $repuesto->decrement('stock', $cantidad);
                        } else {
                            throw new \Exception("Stock insuficiente para repuesto ID {$repuestoId}");
                        }
                    }
                }
            }
        });

        if ($informe) {
            return redirect()
                ->route('informes.correctivo.show', $informe->id)
                ->with('status', 'Informe correctivo generado exitosamente.');
        }

        return back()
            ->withErrors(['general' => 'Error al generar el informe.'])
            ->withInput();
    }

    // =========================================================================
    // INFORME PREVENTIVO
    // =========================================================================

    public function showPreventivo(int $id): View
    {
        $informe = InformePreventivo::with([
            'centroMedico',
            'equipo',
            'usuario',
            'inspecciones',
        ])->findOrFail($id);

        return view('informes.show-preventivo', compact('informe'));
    }

    public function storePreventivo(Request $request): Response|RedirectResponse
    {
        $request->validate([
            'fecha'                 => 'required|date',
            'centro_medico_id'      => 'required|exists:centros_medicos,id',
            'equipo_id'             => 'required|exists:equipos,id',
            'numero_inventario'     => 'required|string',
            'horas_operacion'       => 'required|integer|min:0',
            'inspecciones'          => 'required|array',
            'inspecciones.*'        => 'required|in:SI,NO,N/A',
            'comentarios'           => 'nullable|string|max:500',
            'fecha_proximo_control' => 'nullable|date',
            'firma_tecnico'         => 'required|string',
            'firma_cliente'         => 'nullable|string',
        ]);

        // Validar horas de operación
        $equipo = Equipo::find($request->equipo_id);
        if ($equipo && $request->horas_operacion < $equipo->horas_uso) {
            return back()
                ->withErrors([
                    'horas_operacion' => 'Las horas de operación deben ser mayores o iguales a las horas actuales del equipo (' . $equipo->horas_uso . ').',
                ])
                ->withInput();
        }

        $informe = null;

        // Generar número de reporte con iniciales ANTES de la transacción
        $numero_reporte_servicio = $this->generarNumeroPreventivo();

        DB::transaction(function () use ($request, &$informe, $numero_reporte_servicio) {
            $informe = InformePreventivo::create([
                'numero_reporte_servicio' => $numero_reporte_servicio,
                'fecha'                   => $request->fecha,
                'usuario_id'              => Auth::id(),
                'centro_medico_id'        => $request->centro_medico_id,
                'equipo_id'               => $request->equipo_id,
                'numero_inventario'       => $request->numero_inventario,
                'comentarios'             => $request->comentarios,
                'fecha_proximo_control'   => $request->fecha_proximo_control,
                'firma_tecnico'           => $request->firma_tecnico,
                'firma_cliente'           => $request->firma_cliente,
            ]);

            // Actualizar horas de uso
            $equipo = Equipo::find($request->equipo_id);
            if ($equipo) {
                $equipo->update(['horas_uso' => $request->horas_operacion]);
            }

            // Inspecciones
            $inspecciones = [
                'Inspección visual y limpieza general.',
                'Revisión de cable de alimentación.',
                'Lubricación de piezas móviles y sellos externos.',
                'Soplado de módulo eléctrico e hidráulico.',
                'Cambio de kit de Mantención.',
                'Reemplazo de O’ring de acopladores del dializador.',
                'Chequeos funcionales y paso del test T1.',
                'Chequeo y Calibración de presión de entrada de agua.',
                'Chequeo y Calibración de presión de carga de cámara de balance.',
                'Chequeo y Calibración de presión de bomba de flujo.',
                'Chequeo y Calibración de presión de Desgasificación.',
                'Chequeo y Calibración de volumen bomba de UF.',
                'Chequeo y Calibración de flujo de 300 ml/min.',
                'Chequeo y Calibración de flujo de 500 ml/min.',
                'Chequeo y Calibración de flujo de 800 ml/min.',
                'Chequeo y Calibración de volumen cámara de Balance.',
                'Chequeo y Calibración de volumen Bomba de Concentrado.',
                'Chequeo y Calibración de Bomba de Bicarbonato.',
                'Chequeo y Calibración de Temperatura.',
                'Chequeo y Calibración de Conductividad con Bibag.',
                'Chequeo y Calibración de Conductividad con Bicarbonato líquido.',
                'Chequeo y Calibración presión arterial.',
                'Chequeo y Calibración presión venosa.',
                'Chequeo y Calibración sensor flujo de sangre.',
                'Chequeo y Calibración sensor detector de aire.',
                'Chequeo y Calibración de funcionamiento y revisión de módulo arterial.',
                'Chequeo y Calibración de funcionamiento y revisión de módulo bomba de heparina.',
                'Chequeo y Calibración de funcionamiento y revisión de módulo venoso.',
                'Chequeo alarma de falla de alimentación-sonido continuo-mensaje en pantalla: Falla de corriente.',
                'Chequeo de cargas de batería de respaldo.',
                'Chequeo de funcionamiento de BPM.',
                'Medición puesta a tierra.',
                'Medición corriente de fuga.',
                'Reemplazo de piezas (si procede).',
                'Lubricación de ruedas.',
            ];

            foreach ($inspecciones as $index => $desc) {
                $respuesta = $request->inspecciones[$index] ?? null;

                $informe->inspecciones()->create([
                    'descripcion' => $desc,
                    'respuesta'   => $respuesta,
                ]);
            }
        });

        if ($informe) {
            return redirect()
                ->route('informes.preventivo.show', $informe->id)
                ->with('status', 'Informe preventivo generado exitosamente.');
        }

        return back()
            ->withErrors(['general' => 'Error al generar el informe.'])
            ->withInput();
    }

    // =========================================================================
    // PDF UNIFICADO: DESCARGAR E IMPRIMIR
    // =========================================================================

    /**
     * Descarga el PDF de un informe correctivo o preventivo.
     *
     * Ruta esperada:
     *   GET /informes/{tipo}/{id}/download
     * donde {tipo} = 'correctivo' | 'preventivo'
     */
    public function downloadPdf(string $tipo, int $id): Response
    {
        $base64Logo = $this->getLogoBase64();

        if ($tipo === 'correctivo') {
            $informe = InformeCorrectivo::with([
                'cliente',
                'centroMedico',
                'equipo',
                'usuario',
                'repuestos',
            ])->findOrFail($id);

            $pdf = Pdf::loadView('pdf.informe-correctivos', [
                'informe'    => $informe,
                'base64Logo' => $base64Logo,
            ]);

            return $pdf->download('informe-correctivo-' . $informe->numero_folio . '.pdf');
        }

        if ($tipo === 'preventivo') {
            $informe = InformePreventivo::with([
                'centroMedico',
                'equipo',
                'usuario',
                'inspecciones',
            ])->findOrFail($id);

            $pdf = Pdf::loadView('pdf.informe-preventivos', [
                'informe'    => $informe,
                'base64Logo' => $base64Logo,
            ]);

            return $pdf->download('informe-preventivo-' . $informe->numero_reporte_servicio . '.pdf');
        }

        abort(404, 'Tipo de informe no válido.');
    }

    /**
     * Muestra la vista PDF para impresión (sin descargar).
     *
     * Ruta esperada:
     *   GET /informes/{tipo}/{id}/print
     * donde {tipo} = 'correctivo' | 'preventivo'
     */
    public function printPdf(string $tipo, int $id): View
    {
        $base64Logo = $this->getLogoBase64();

        if ($tipo === 'correctivo') {
            $informe = InformeCorrectivo::with([
                'cliente',
                'centroMedico',
                'equipo',
                'usuario',
                'repuestos',
            ])->findOrFail($id);

            return view('pdf.informe-correctivos', [
                'informe'    => $informe,
                'base64Logo' => $base64Logo,
            ]);
        }

        if ($tipo === 'preventivo') {
            $informe = InformePreventivo::with([
                'centroMedico',
                'equipo',
                'usuario',
                'inspecciones',
            ])->findOrFail($id);

            return view('pdf.informe-preventivos', [
                'informe'    => $informe,
                'base64Logo' => $base64Logo,
            ]);
        }

        abort(404, 'Tipo de informe no válido.');
    }
}
