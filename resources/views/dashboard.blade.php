<x-layouts.app :title="__('Dashboard')">
    @vite(['resources/css/dashboard.css'])
    @php
        $rangeOptions = [
            7 => 'Últimos 7 días',
            30 => 'Últimos 30 días',
            90 => 'Últimos 90 días',
            180 => 'Últimos 180 días',
            365 => 'Últimos 12 meses',
        ];

        $seccionesDisponibles = [
            'machines' => 'Máquinas',
            'informes' => 'Informes',
            'clients' => 'Clientes',
            'spares' => 'Repuestos',
        ];

        $seccionesSeleccionadas = collect((array) request('secciones', array_keys($seccionesDisponibles)))
            ->filter(fn($seccion) => array_key_exists($seccion, $seccionesDisponibles))
            ->values()
            ->all();

        if (empty($seccionesSeleccionadas)) {
            $seccionesSeleccionadas = array_keys($seccionesDisponibles);
        }

        $mostrarSeccion = fn($clave) => in_array($clave, $seccionesSeleccionadas, true);

        $rangoSeleccionado = request()->integer('rango', 30);
        if (!array_key_exists($rangoSeleccionado, $rangeOptions)) {
            $rangoSeleccionado = 30;
        }

        $fechaInicio = now()
            ->startOfDay()
            ->subDays($rangoSeleccionado - 1);

        $estadosValidos = ['Operativo', 'En observacion', 'Fuera de servicio', 'Baja'];
        $estadosSeleccionados = collect(request('resaltar_estado', $estadosValidos))
            ->filter(fn($estado) => in_array($estado, $estadosValidos, true))
            ->values()
            ->all();

        if (empty($estadosSeleccionados)) {
            $estadosSeleccionados = $estadosValidos;
        }

        $equiposPorEstado = \App\Models\Equipo::select(
            'estado',
            \Illuminate\Support\Facades\DB::raw('COUNT(*) as total'),
        )
            ->whereIn('estado', $estadosValidos)
            ->groupBy('estado')
            ->pluck('total', 'estado');

        $estadoMeta = [
            'Operativo' => [
                'label' => 'En funcionamiento',
                'descripcion' => 'Equipos disponibles para operar',
                'color' => 'emerald',
            ],
            'En observacion' => [
                'label' => 'En revisión',
                'descripcion' => 'Requieren seguimiento cercano',
                'color' => 'amber',
            ],
            'Fuera de servicio' => [
                'label' => 'Fuera de servicio',
                'descripcion' => 'No disponibles hasta nueva mantención',
                'color' => 'red',
            ],
            'Baja' => [
                'label' => 'Dados de baja',
                'descripcion' => 'Equipos retirados del inventario activo',
                'color' => 'slate',
            ],
        ];

        $estadoCards = collect($estadosSeleccionados)
            ->map(function ($estado) use ($estadoMeta, $equiposPorEstado) {
                $meta = $estadoMeta[$estado] ?? [
                    'label' => $estado,
                    'descripcion' => 'Estado personalizado',
                    'color' => 'zinc',
                ];

                return [
                    'key' => $estado,
                    'label' => $meta['label'],
                    'descripcion' => $meta['descripcion'],
                    'color' => $meta['color'],
                    'valor' => $equiposPorEstado[$estado] ?? 0,
                ];
            })
            ->values()
            ->all();

        $equiposTotal = collect($estadoCards)->sum('valor');
        $estadoValor = fn($estado) => in_array($estado, $estadosSeleccionados, true)
            ? $equiposPorEstado[$estado] ?? 0
            : 0;
        $equiposOperativos = $estadoValor('Operativo');
        $equiposRevision = $estadoValor('En observacion');
        $equiposFueraServicio = $estadoValor('Fuera de servicio');
        $equiposBaja = $estadoValor('Baja');

        $informesCorrectivosTotal = \App\Models\InformeCorrectivo::count();
        $informesPreventivosTotal = \App\Models\InformePreventivo::count();
        $informesPeriodoCorrectivos = \App\Models\InformeCorrectivo::whereDate(
            'fecha_servicio',
            '>=',
            $fechaInicio,
        )->count();
        $informesPeriodoPreventivos = \App\Models\InformePreventivo::whereDate('fecha', '>=', $fechaInicio)->count();

        $clientesTotal = \App\Models\Cliente::count();
        $clientesConCentros = \App\Models\Cliente::has('centrosMedicos')->count();
        $topClientes = \App\Models\Cliente::withCount('centrosMedicos')
            ->orderByDesc('centros_medicos_count')
            ->take(5)
            ->get();

        $repuestosTotal = \App\Models\Repuesto::count();
        $repuestosStock = \App\Models\Repuesto::sum('stock');
        $repuestosCriticos = \App\Models\Repuesto::whereBetween('stock', [1, 2])->count();
        $repuestosBajos = \App\Models\Repuesto::whereBetween('stock', [3, 5])->count();
        $repuestosSinStock = \App\Models\Repuesto::where('stock', 0)->count();
        $repuestosPorSalir = \App\Models\Repuesto::orderBy('stock')
            ->take(5)
            ->get(['id', 'nombre', 'stock']);

        $equiposCriticos = \App\Models\Equipo::select(
            'id',
            'nombre',
            'codigo',
            'estado',
            'cant_dias_fuera_serv',
            'updated_at',
        )
            ->whereIn('estado', ['Fuera de servicio', 'En observacion'])
            ->latest('updated_at')
            ->take(5)
            ->get();

        $clienteFiltro = request()->integer('cliente_filtro');
        $centroFiltro = request()->integer('centro_filtro');
        $estadoFiltroSeleccionado = request()->input('estado_filtro');
        $estadoFiltroSeleccionado = in_array($estadoFiltroSeleccionado, $estadosValidos, true)
            ? $estadoFiltroSeleccionado
            : null;
        $numeroSerieFiltro = trim((string) request()->input('numero_serie_filtro', ''));
        $clienteEstadoChart = request()->integer('cliente_estado_chart');

        $equiposMetricas = \App\Models\Equipo::with(['centro.cliente'])
            ->when($clienteFiltro, fn($q) => $q->whereHas('centro', fn($w) => $w->where('cliente_id', $clienteFiltro)))
            ->when($centroFiltro, fn($q) => $q->where('centro_medico_id', $centroFiltro))
            ->when($estadoFiltroSeleccionado, fn($q) => $q->where('estado', $estadoFiltroSeleccionado))
            ->when($numeroSerieFiltro !== '', fn($q) => $q->where('numero_serie', 'like', "%{$numeroSerieFiltro}%"))
            ->orderBy('nombre')
            ->get();

        $clientesFiltroOpciones = \App\Models\Cliente::orderBy('nombre')->get(['id', 'nombre']);
        $centrosFiltroOpciones = \App\Models\CentroMedico::with('cliente:id,nombre')
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'cliente_id']);
        $clienteEstadoChartNombre = $clienteEstadoChart
            ? optional($clientesFiltroOpciones->firstWhere('id', $clienteEstadoChart))->nombre
            : null;

        $equiposEstadoCliente = \App\Models\Equipo::select(
            'estado',
            \Illuminate\Support\Facades\DB::raw('COUNT(*) as total'),
        )
            ->whereIn('estado', $estadosValidos)
            ->when(
                $clienteEstadoChart,
                fn($q) => $q->whereHas('centro', fn($w) => $w->where('cliente_id', $clienteEstadoChart)),
            )
            ->groupBy('estado')
            ->pluck('total', 'estado');

        $estadoColorHex = [
            'Operativo' => '#10b981',
            'En observacion' => '#f59e0b',
            'Fuera de servicio' => '#ef4444',
            'Baja' => '#64748b',
        ];

        $chartEquiposEstado = [
            'keys' => $estadosValidos,
            'labels' => collect($estadosValidos)
                ->map(fn($estado) => $estadoMeta[$estado]['label'] ?? $estado)
                ->toArray(),
            'values' => collect($estadosValidos)->map(fn($estado) => $equiposEstadoCliente[$estado] ?? 0)->toArray(),
            'colors' => collect($estadosValidos)->map(fn($estado) => $estadoColorHex[$estado] ?? '#94a3b8')->toArray(),
        ];
        $chartEquiposEstadoTotal = array_sum($chartEquiposEstado['values']);
        $chartEquiposEstado['total'] = $chartEquiposEstadoTotal;
        $chartEquiposEstado['cliente'] = $clienteEstadoChartNombre;

        $totalesPorCliente = $equiposMetricas
            ->groupBy(function ($equipo) {
                return optional(optional($equipo->centro)->cliente)->nombre ?? 'Sin cliente asignado';
            })
            ->map->count()
            ->sortDesc();

        $resumenMetricas = [
            'total_maquinas' => $equiposMetricas->count(),
            'total_clientes' => $equiposMetricas
                ->map(fn($equipo) => optional(optional($equipo->centro)->cliente)->id)
                ->filter()
                ->unique()
                ->count(),
        ];

        $filtrosMetricasPersistentes = request()->except([
            'cliente_filtro',
            'centro_filtro',
            'estado_filtro',
            'numero_serie_filtro',
        ]);

        $filtrosChartEstadoPersistentes = request()->except(['cliente_estado_chart']);
        $equiposEstadoChartUrl = Route::has('dashboard.charts.equipos-estado')
            ? route('dashboard.charts.equipos-estado')
            : url('dashboard/charts/equipos-estado');

        $clienteInformesFiltro = request()->integer('cliente_informes');
        $informesDesdeRaw = trim((string) request()->input('informes_desde', ''));
        $informesHastaRaw = trim((string) request()->input('informes_hasta', ''));

        $parseFechaFiltro = function ($valor, $finDeDia = false) {
            if ($valor === '') {
                return null;
            }

            try {
                $fecha = \Carbon\Carbon::createFromFormat('d/m/Y', $valor);
                return $finDeDia ? $fecha->endOfDay() : $fecha->startOfDay();
            } catch (\Throwable $th) {
                return null;
            }
        };

        $informesDesde = $parseFechaFiltro($informesDesdeRaw);
        $informesHasta = $parseFechaFiltro($informesHastaRaw, true);

        if ($informesDesde && $informesHasta && $informesHasta->lessThan($informesDesde)) {
            [$informesDesde, $informesHasta] = [
                $informesHasta->copy()->startOfDay(),
                $informesDesde->copy()->endOfDay(),
            ];
        }

        if (!$informesHasta) {
            $informesHasta = now()->endOfDay();
        }

        if (!$informesDesde) {
            $informesDesde = $informesHasta->copy()->subMonths(11)->startOfMonth();
        }

        $mesesReferencia = collect();
        $cursor = $informesDesde->copy()->startOfMonth();
        $finMes = $informesHasta->copy()->startOfMonth();
        while ($cursor <= $finMes) {
            $mesesReferencia->push($cursor->copy());
            $cursor->addMonth();
        }

        if ($mesesReferencia->isEmpty()) {
            $mesesReferencia->push($informesDesde->copy()->startOfMonth());
        }

        $informesDesdeInput =
            $informesDesdeRaw !== ''
                ? ($informesDesde
                    ? $informesDesde->copy()->startOfDay()->format('d/m/Y')
                    : $informesDesdeRaw)
                : '';
        $informesHastaInput =
            $informesHastaRaw !== ''
                ? ($informesHasta
                    ? $informesHasta->copy()->startOfDay()->format('d/m/Y')
                    : $informesHastaRaw)
                : '';

        $filtrosInformesPersistentes = request()->except(['cliente_informes', 'informes_desde', 'informes_hasta']);

        $fallasPorTipo = \App\Models\InformeCorrectivo::selectRaw(
            "COALESCE(NULLIF(TRIM(problema_informado), ''), 'Sin clasificación') as tipo_falla, COUNT(*) as total",
        )
            ->when($clienteInformesFiltro, fn($q) => $q->where('cliente_id', $clienteInformesFiltro))
            ->groupBy('tipo_falla')
            ->orderByDesc('total')
            ->take(7)
            ->get();

        $correctivosPorMes = \App\Models\InformeCorrectivo::select(
            \Illuminate\Support\Facades\DB::raw("DATE_FORMAT(fecha_servicio, '%Y-%m') as periodo"),
            \Illuminate\Support\Facades\DB::raw('COUNT(*) as total'),
        )
            ->whereDate('fecha_servicio', '>=', $informesDesde)
            ->whereDate('fecha_servicio', '<=', $informesHasta)
            ->when($clienteInformesFiltro, fn($q) => $q->where('cliente_id', $clienteInformesFiltro))
            ->groupBy('periodo')
            ->get()
            ->pluck('total', 'periodo');

        $preventivosPorMes = \App\Models\InformePreventivo::select(
            \Illuminate\Support\Facades\DB::raw("DATE_FORMAT(fecha, '%Y-%m') as periodo"),
            \Illuminate\Support\Facades\DB::raw('COUNT(*) as total'),
        )
            ->whereDate('fecha', '>=', $informesDesde)
            ->whereDate('fecha', '<=', $informesHasta)
            ->when($clienteInformesFiltro, function ($q) use ($clienteInformesFiltro) {
                $q->whereHas('centroMedico', fn($w) => $w->where('cliente_id', $clienteInformesFiltro));
            })
            ->groupBy('periodo')
            ->get()
            ->pluck('total', 'periodo');

        $chartInformesMes = [
            'labels' => $mesesReferencia->map(fn($mes) => $mes->translatedFormat('M Y'))->toArray(),
            'correctivos' => $mesesReferencia->map(fn($mes) => $correctivosPorMes[$mes->format('Y-m')] ?? 0)->toArray(),
            'preventivos' => $mesesReferencia->map(fn($mes) => $preventivosPorMes[$mes->format('Y-m')] ?? 0)->toArray(),
        ];

        $chartFallasData = [
            'labels' => $fallasPorTipo->pluck('tipo_falla')->toArray(),
            'values' => $fallasPorTipo->pluck('total')->toArray(),
        ];

        $repuestosMasUsados = \Illuminate\Support\Facades\DB::table('informe_correctivo_repuesto as icr')
            ->join('repuestos as r', 'r.id', '=', 'icr.repuesto_id')
            ->select('r.nombre', \Illuminate\Support\Facades\DB::raw('SUM(COALESCE(icr.cantidad_usada, 0)) as total'))
            ->groupBy('r.nombre')
            ->orderByDesc('total')
            ->take(7)
            ->get();

        $chartRepuestos = [
            'labels' => $repuestosMasUsados->pluck('nombre')->toArray(),
            'values' => $repuestosMasUsados->pluck('total')->toArray(),
        ];

    @endphp

    <div>
        <div class="dashboard-page">
            <div class="dashboard-shell flex flex-col gap-6">
                <div class="glass-card p-6">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <p class="text-sm mb-2 font-semibold uppercase tracking-wide text-blue-600">Dashboard</p>
                            <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-50">Visión general de operaciones
                            </h1>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">Controla equipos, informes, clientes y
                                repuestos
                                desde un solo lugar.</p>
                        </div>
                        <div class="text-sm text-zinc-500 dark:text-zinc-400">
                            Actualizado al <span
                                class="font-semibold text-zinc-800 dark:text-zinc-100">{{ now()->format('d M Y \a \l\a\s H:i') }}</span>
                        </div>
                    </div>

                    <form id="dashboard-filters" method="GET" class="mt-6 space-y-4">
                        <div class="grid gap-4 md:grid-cols-3">
                            <div>
                                <label for="rango" class="text-sm font-medium text-zinc-600 dark:text-zinc-300">Rango
                                    temporal</label>
                                <select id="rango" name="rango"
                                    class="mt-1 w-full rounded-xl border border-zinc-300 bg-white px-4 py-2 text-sm font-medium text-zinc-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                                    @foreach ($rangeOptions as $value => $label)
                                        <option value="{{ $value }}" @selected($rangoSeleccionado === $value)>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <span class="text-sm font-medium text-zinc-600 dark:text-zinc-300">Estados
                                    incluidos</span>
                                <div class="mt-2 flex flex-wrap gap-3">
                                    @foreach ($estadosValidos as $estado)
                                        <label
                                            class="inline-flex items-center gap-2 rounded-full border border-zinc-200 px-3 py-1 text-xs font-semibold text-zinc-600 transition hover:border-blue-400 hover:text-blue-600 dark:border-zinc-700 dark:text-zinc-300">
                                            <input type="checkbox" name="resaltar_estado[]" value="{{ $estado }}"
                                                class="size-4 rounded border-zinc-300 text-blue-600 focus:ring-blue-500 dark:border-zinc-600"
                                                @checked(in_array($estado, $estadosSeleccionados, true))>
                                            {{ $estado }}
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div>
                            <span class="text-sm font-medium text-zinc-600 dark:text-zinc-300">Secciones visibles</span>
                            <div class="mt-2 flex flex-wrap gap-3">
                                @foreach ($seccionesDisponibles as $clave => $label)
                                    <label
                                        class="inline-flex items-center gap-2 rounded-full border border-zinc-200 px-3 py-1 text-xs font-semibold text-zinc-600 transition hover:border-blue-400 hover:text-blue-600 dark:border-zinc-700 dark:text-zinc-300">
                                        <input type="checkbox" name="secciones[]" value="{{ $clave }}"
                                            class="size-4 rounded border-zinc-300 text-blue-600 focus:ring-blue-500 dark:border-zinc-600"
                                            @checked(in_array($clave, $seccionesSeleccionadas, true))>
                                        {{ $label }}
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-3 text-xs text-zinc-500 dark:text-zinc-400">
                            <span class="inline-flex items-center gap-1">
                                <i class="fa-solid fa-bolt text-yellow-400"></i>
                                Los cambios se aplican automáticamente.
                            </span>
                            <a href="{{ route('dashboard') }}"
                                class="inline-flex items-center gap-2 rounded-xl border border-transparent px-5 py-2 text-sm font-semibold text-zinc-600 hover:border-zinc-300 dark:text-zinc-300">
                                Reiniciar filtros
                            </a>
                        </div>
                    </form>
                </div>

                @if ($mostrarSeccion('machines'))
                    <section data-section="machines" class="space-y-6">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-blue-600">Sección</p>
                                <h2 class="text-2xl font-bold text-white">Máquinas</h2>
                                <p class="text-sm text-zinc-100 dark:text-zinc-400">Visión consolidada por estado y
                                    cliente.</p>
                            </div>
                        </div>

                        <div class="glass-card p-5">
                            <form method="GET" class="flex flex-wrap items-end gap-4">
                                @foreach ($filtrosMetricasPersistentes as $param => $valor)
                                    @if (is_array($valor))
                                        @foreach ($valor as $subValor)
                                            <input type="hidden" name="{{ $param }}[]"
                                                value="{{ $subValor }}">
                                        @endforeach
                                    @else
                                        <input type="hidden" name="{{ $param }}" value="{{ $valor }}">
                                    @endif
                                @endforeach

                                <div class="flex-1 min-w-[200px]">
                                    <label for="cliente_filtro"
                                        class="text-xs font-semibold uppercase tracking-wide text-zinc-500">Cliente</label>
                                    <select id="cliente_filtro" name="cliente_filtro"
                                        class="mt-1 w-full rounded-xl border border-zinc-200 bg-white px-3 py-2 text-sm font-medium text-zinc-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                                        <option value="">Todos</option>
                                        @foreach ($clientesFiltroOpciones as $cliente)
                                            <option value="{{ $cliente->id }}" @selected($clienteFiltro === $cliente->id)>
                                                {{ $cliente->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="flex-1 min-w-[200px]">
                                    <label for="centro_filtro"
                                        class="text-xs font-semibold uppercase tracking-wide text-zinc-500">Centro
                                        médico</label>
                                    <select id="centro_filtro" name="centro_filtro"
                                        class="mt-1 w-full rounded-xl border border-zinc-200 bg-white px-3 py-2 text-sm font-medium text-zinc-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                                        <option value="">Todos</option>
                                        @foreach ($centrosFiltroOpciones as $centro)
                                            <option value="{{ $centro->id }}" @selected($centroFiltro === $centro->id)>
                                                {{ $centro->nombre }}
                                                @if (optional($centro->cliente)->nombre)
                                                    ({{ $centro->cliente->nombre }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="flex-1 min-w-[200px]">
                                    <label for="estado_filtro"
                                        class="text-xs font-semibold uppercase tracking-wide text-zinc-500">Estado</label>
                                    <select id="estado_filtro" name="estado_filtro"
                                        class="mt-1 w-full rounded-xl border border-zinc-200 bg-white px-3 py-2 text-sm font-medium text-zinc-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                                        <option value="">Todos</option>
                                        @foreach ($estadosValidos as $estado)
                                            <option value="{{ $estado }}" @selected($estadoFiltroSeleccionado === $estado)>
                                                {{ $estado }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="flex-1 min-w-[200px]">
                                    <label for="numero_serie_filtro"
                                        class="text-xs font-semibold uppercase tracking-wide text-zinc-500">Número de
                                        serie</label>
                                    <input id="numero_serie_filtro" name="numero_serie_filtro" type="text"
                                        value="{{ $numeroSerieFiltro }}"
                                        class="mt-1 w-full rounded-xl border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100"
                                        placeholder="Ej. SN-00123">
                                </div>

                                <div class="flex items-center gap-3 text-sm">
                                    <button type="submit"
                                        class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2 font-semibold text-white shadow hover:bg-blue-500">
                                        <i class="fa-solid fa-magnifying-glass"></i>
                                        Aplicar filtros
                                    </button>
                                    <a href="{{ route('dashboard', $filtrosMetricasPersistentes) }}"
                                        class="inline-flex items-center gap-2 rounded-xl px-4 py-2 font-semibold text-zinc-600 hover:text-blue-600 dark:text-zinc-300">
                                        <i class="fa-solid fa-rotate-left"></i>
                                        Limpiar
                                    </a>
                                </div>
                            </form>
                        </div>

                        <div class="dashboard-metrics-grid dashboard-metrics-grid--thirds items-start">
                            <div class="glass-card glass-card--muted rounded-2xl p-5 shadow-sm">
                                <div class="flex flex-wrap items-center justify-between gap-4">
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-wide text-blue-500">Resumen
                                            filtrado
                                        </p>
                                        <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-50">Métrica de
                                            máquinas
                                            por
                                            cliente</h2>
                                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Números totales de la vista
                                            actual.
                                        </p>
                                    </div>
                                    <div class="flex gap-6 text-sm">
                                        <div>
                                            <p class="text-xs uppercase text-zinc-500">Máquinas</p>
                                            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-50">
                                                {{ number_format($resumenMetricas['total_maquinas']) }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs uppercase text-zinc-500">Clientes</p>
                                            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-50">
                                                {{ number_format($resumenMetricas['total_clientes']) }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="glass-card glass-card--muted rounded-2xl p-5 shadow-sm">
                                <div class="flex items-center justify-between">
                                    <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-50">Distribución por
                                        estado
                                    </h2>
                                    <span
                                        class="text-xs text-zinc-500">{{ $equiposTotal ? '100%' : 'Sin datos' }}</span>
                                </div>
                                <div class="mt-4 space-y-4">
                                    @foreach ($estadoCards as $card)
                                        @php
                                            $valorEstado = $card['valor'];
                                            $porcentaje =
                                                $equiposTotal > 0 ? round(($valorEstado / $equiposTotal) * 100) : 0;
                                        @endphp
                                        <div>
                                            <div
                                                class="flex items-center justify-between text-sm font-semibold text-zinc-600 dark:text-zinc-300">
                                                <span>{{ $card['label'] }}</span>
                                                <span>{{ number_format($valorEstado) }} ({{ $porcentaje }}%)</span>
                                            </div>
                                            <div class="mt-2 h-2 rounded-full bg-zinc-100 dark:bg-zinc-800">
                                                <div class="h-2 rounded-full bg-{{ $card['color'] }}-500"
                                                    style="width: {{ $porcentaje }}%"></div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="glass-card glass-card--muted rounded-2xl p-5 shadow-sm">
                                <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-50">Resumen de actividad
                                </h2>
                                <ul class="mt-4 space-y-3 text-sm">
                                    @foreach ($estadoMeta as $estado => $meta)
                                        @php
                                            $habilitado = in_array($estado, $estadosSeleccionados, true);
                                            $valor = $habilitado ? $equiposPorEstado[$estado] ?? 0 : 0;
                                        @endphp
                                        <li
                                            class="flex items-center justify-between {{ $habilitado ? 'text-zinc-600 dark:text-zinc-300' : 'text-zinc-400 dark:text-zinc-600 italic' }}">
                                            <span>{{ $meta['label'] }}</span><strong>{{ number_format($valor) }}</strong>
                                        </li>
                                    @endforeach
                                </ul>
                                <div
                                    class="mt-5 rounded-xl bg-blue-50 p-4 text-xs text-blue-800 dark:bg-blue-900/30 dark:text-blue-200">
                                    <p>Consejo: usa las casillas de "Resaltar estados" para centrar tu atención en los
                                        estados
                                        críticos.</p>
                                </div>
                            </div>
                        </div>

                        <div class="glass-card glass-card--muted rounded-2xl p-5 shadow-sm">
                            <div class="flex flex-wrap items-center justify-between gap-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-50">Detalle de
                                        máquinas
                                        filtradas
                                    </h3>
                                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Tabla completa según los
                                        filtros
                                        aplicados.
                                    </p>
                                </div>
                                <span
                                    class="text-xs font-semibold uppercase tracking-wide text-zinc-500">{{ number_format($equiposMetricas->count()) }}
                                    registros</span>
                            </div>

                            <div class="mt-4 overflow-x-auto rounded-xl border border-zinc-100 dark:border-zinc-800">
                                <table class="w-full min-w-[720px] text-left text-sm">
                                    <thead
                                        class="bg-zinc-50 text-xs uppercase tracking-wide text-zinc-500 dark:bg-zinc-900/40">
                                        <tr>
                                            <th class="px-4 py-2">Cliente</th>
                                            <th class="px-4 py-2">Centro médico</th>
                                            <th class="px-4 py-2">Máquina</th>
                                            <th class="px-4 py-2">Estado</th>
                                            <th class="px-4 py-2">N° serie</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                                        @forelse ($equiposMetricas as $equipo)
                                            @php
                                                $clienteNombre =
                                                    optional(optional($equipo->centro)->cliente)->nombre ??
                                                    'Sin cliente asignado';
                                                $centroNombre =
                                                    optional($equipo->centro)->nombre ?? 'Sin centro asociado';
                                            @endphp
                                            <tr class="text-zinc-700 dark:text-zinc-200">
                                                <td class="px-4 py-2 font-semibold">{{ $clienteNombre }}</td>
                                                <td class="px-4 py-2">{{ $centroNombre }}</td>
                                                <td class="px-4 py-2">{{ $equipo->nombre }}</td>
                                                <td class="px-4 py-2">
                                                    <span
                                                        class="rounded-full bg-zinc-100 px-3 py-0.5 text-xs font-semibold text-zinc-600 dark:bg-zinc-800 dark:text-zinc-200">
                                                        {{ $equipo->estado ?? 'Sin estado' }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-2 text-xs text-zinc-500">
                                                    {{ $equipo->numero_serie ?? 'N/D' }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5"
                                                    class="px-4 py-4 text-center text-sm text-zinc-500">Sin
                                                    resultados para los filtros aplicados.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-6 rounded-xl border border-dashed border-zinc-200 p-4 dark:border-zinc-700">
                                <h4 class="text-sm font-semibold text-zinc-900 dark:text-zinc-50">Total de máquinas por
                                    cliente
                                </h4>
                                @if ($totalesPorCliente->isEmpty())
                                    <p class="mt-3 text-sm text-zinc-500">No hay datos para mostrar.</p>
                                @else
                                    <ul class="mt-4 space-y-3 text-sm">
                                        @foreach ($totalesPorCliente->take(5) as $clienteNombre => $total)
                                            <li class="flex items-center justify-between">
                                                <span
                                                    class="text-zinc-600 dark:text-zinc-300">{{ $clienteNombre }}</span>
                                                <span
                                                    class="font-semibold text-zinc-900 dark:text-zinc-50">{{ number_format($total) }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                    @if ($totalesPorCliente->count() > 5)
                                        <p class="mt-3 text-xs text-zinc-500">Mostrando los 5 principales clientes.</p>
                                    @endif
                                @endif
                            </div>
                        </div>

                        <div class="glass-card glass-card--muted rounded-2xl p-5 shadow-sm" id="equipos-estado-card"
                            @if ($equiposEstadoChartUrl) data-chart-url="{{ $equiposEstadoChartUrl }}" @endif>
                            <div class="flex flex-wrap items-center justify-between gap-4">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-blue-600">
                                        Visualización</p>
                                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-50">Equipos por
                                        estado</h3>
                                    <p class="text-sm text-zinc-500 dark:text-zinc-400">
                                        <span id="equipos-estado-cliente-text">
                                            {{ $clienteEstadoChartNombre ? 'Cliente seleccionado: ' . $clienteEstadoChartNombre : 'Mostrando todos los clientes' }}
                                        </span>
                                        <button type="button" id="equipos-estado-reset"
                                            class="ml-2 text-xs font-semibold text-blue-600 hover:text-blue-500 hidden">
                                            Limpiar
                                        </button>
                                    </p>
                                </div>

                                <div class="min-w-[220px]" data-chart-filter>
                                    <label for="cliente_estado_chart"
                                        class="text-xs font-semibold uppercase tracking-wide text-zinc-500">Cliente</label>
                                    <select id="cliente_estado_chart" name="cliente_estado_chart"
                                        class="mt-1 w-full rounded-xl border border-zinc-200 bg-white px-3 py-2 text-sm font-medium text-zinc-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100"
                                        data-chart-filter-select>
                                        <option value="">Todos</option>
                                        @foreach ($clientesFiltroOpciones as $cliente)
                                            <option value="{{ $cliente->id }}" @selected($clienteEstadoChart === $cliente->id)>
                                                {{ $cliente->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="mt-6">
                                <div class="grid gap-6 lg:grid-cols-[2fr,1fr]" id="equipos-estado-wrapper"
                                    data-chart-container>
                                    <div class="min-h-[260px]">
                                        <canvas id="chart-equipos-estado" height="240"></canvas>
                                    </div>
                                    <div class="space-y-4 text-sm">
                                        <div>
                                            <p class="text-xs uppercase text-zinc-500">Total equipos</p>
                                            <p class="text-3xl font-bold text-zinc-900 dark:text-white"
                                                id="equipos-estado-total">
                                                {{ number_format($chartEquiposEstadoTotal) }}
                                            </p>
                                        </div>
                                        <ul class="space-y-3" id="equipos-estado-lista">
                                            @foreach ($estadoMeta as $estado => $meta)
                                                @php
                                                    $indice = array_search($estado, $chartEquiposEstado['keys'], true);
                                                    $valorEstado =
                                                        $indice !== false ? $chartEquiposEstado['values'][$indice] : 0;
                                                @endphp
                                                <li class="flex items-center justify-between"
                                                    data-estado="{{ $estado }}">
                                                    <span
                                                        class="text-zinc-600 dark:text-zinc-300">{{ $meta['label'] }}</span>
                                                    <span class="font-semibold text-zinc-800 dark:text-zinc-100">
                                                        <span class="equipos-estado-valor">
                                                            {{ number_format($valorEstado) }}
                                                        </span>
                                                    </span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                                <p class="text-sm text-zinc-500 hidden" id="equipos-estado-empty">
                                    No hay datos disponibles para los filtros aplicados.
                                </p>
                            </div>
                        </div>
                    </section>
                @endif

                @if ($mostrarSeccion('informes'))
                    <section data-section="informes" class="space-y-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-purple-600">Sección</p>
                            <h2 class="text-2xl font-bold text-white">Informes</h2>
                            <p class="text-sm text-zinc-100 dark:text-zinc-400">Seguimiento de informes correctivos y
                                preventivos.
                            </p>
                        </div>

                        <div class="glass-card p-5">
                            <form method="GET" class="flex flex-wrap items-end gap-4">
                                @foreach ($filtrosInformesPersistentes as $param => $valor)
                                    @if (is_array($valor))
                                        @foreach ($valor as $subValor)
                                            <input type="hidden" name="{{ $param }}[]"
                                                value="{{ $subValor }}">
                                        @endforeach
                                    @else
                                        <input type="hidden" name="{{ $param }}"
                                            value="{{ $valor }}">
                                    @endif
                                @endforeach

                                <div class="flex-1 min-w-[200px]">
                                    <label for="cliente_informes"
                                        class="text-xs font-semibold uppercase tracking-wide text-zinc-500">Filtrar por
                                        cliente</label>
                                    <select id="cliente_informes" name="cliente_informes"
                                        class="mt-1 w-full rounded-xl border border-zinc-200 bg-white px-3 py-2 text-sm font-medium text-zinc-800 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                                        <option value="">Todos los clientes</option>
                                        @foreach ($clientesFiltroOpciones as $cliente)
                                            <option value="{{ $cliente->id }}" @selected($clienteInformesFiltro === $cliente->id)>
                                                {{ $cliente->nombre }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-xs text-zinc-500">Últimos registros</span>
                                </div>
                                <div class="flex items-center gap-3 text-sm">
                                    <button type="submit"
                                        class="inline-flex items-center gap-2 rounded-xl bg-purple-600 px-4 py-2 font-semibold text-white shadow hover:bg-purple-500">
                                        <i class="fa-solid fa-filter"></i>
                                        Aplicar
                                    </button>
                                    <a href="{{ route('dashboard', $filtrosInformesPersistentes) }}"
                                        class="inline-flex items-center gap-2 rounded-xl px-4 py-2 font-semibold text-zinc-600 hover:text-purple-600 dark:text-zinc-300">
                                        <i class="fa-solid fa-rotate-left"></i>
                                        Limpiar
                                    </a>
                                </div>
                            </form>
                        </div>
                        <div class="glass-card p-6">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-50">Informes por mes</h3>
                            </div>
                            <span class="text-xs text-zinc-500">Últimos 12 meses</span>
                            <div class="mt-4">
                                <canvas id="chart-informes-mes" height="220"></canvas>
                            </div>

                        </div>

                        <div class="glass-card p-6">
                            <div class="flex flex-wrap items-center justify-between gap-4">
                                <div>
                                    <p class="text-sm font-semibold uppercase tracking-wide text-purple-600">Informes
                                        técnicos
                                    </p>
                                    <h2 class="text-xl font-bold text-zinc-900 dark:text-zinc-50">Correctivos y
                                        preventivos
                                    </h2>
                                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Rango seleccionado:
                                        {{ $rangeOptions[$rangoSeleccionado] }}</p>
                                </div>
                                <div class="flex items-center gap-4 text-sm">
                                    <div>
                                        <p class="text-xs uppercase text-zinc-500">Total correctivos</p>
                                        <p class="text-2xl font-bold text-zinc-900 dark:text-white">
                                            {{ number_format($informesCorrectivosTotal) }}</p>
                                    </div>
                                    <div class="h-12 w-px bg-zinc-200 dark:bg-zinc-700"></div>
                                    <div>
                                        <p class="text-xs uppercase text-zinc-500">Total preventivos</p>
                                        <p class="text-2xl font-bold text-zinc-900 dark:text-white">
                                            {{ number_format($informesPreventivosTotal) }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 grid gap-4 md:grid-cols-2">
                                <div class="glass-card p-5">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-50">Actividad
                                            reciente
                                        </h3>
                                        <span
                                            class="text-sm text-zinc-500">{{ $rangeOptions[$rangoSeleccionado] }}</span>
                                    </div>
                                    <dl class="mt-4 grid grid-cols-2 gap-4 text-sm">
                                        <div class="rounded-xl bg-purple-200 p-4 dark:bg-purple-900/20">
                                            <dt class="text-xs uppercase text-purple-600">Correctivos</dt>
                                            <dd class="text-2xl font-bold text-purple-800 dark:text-purple-600">
                                                {{ number_format($informesPeriodoCorrectivos) }}</dd>
                                        </div>
                                        <div class="rounded-xl bg-emerald-200 p-4 dark:bg-emerald-900/20">
                                            <dt class="text-xs uppercase text-emerald-600">Preventivos</dt>
                                            <dd class="text-2xl font-bold text-emerald-800 dark:text-emerald-600">
                                                {{ number_format($informesPeriodoPreventivos) }}</dd>
                                        </div>
                                    </dl>
                                    <p class="mt-4 text-xs text-zinc-500">Los valores consideran informes con fecha
                                        igual o
                                        posterior
                                        al {{ $fechaInicio->format('d/m/Y') }}.</p>
                                </div>

                                <div class="glass-card p-5">
                                    <div class="grid gap-4 lg:grid-cols-2">
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm font-semibold uppercase tracking-wide text-emerald-600">
                                                Actividad
                                                mensual
                                            </p>
                                        </div>
                                        <div class="mt-4 space-y-4">
                                            <span class="text-xs text-zinc-500">Últimos 12 meses</span>

                                            <div class="flex items-center justify-between">
                                                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-50">
                                                    Distribución
                                                    por
                                                    estado</h3>
                                                <span
                                                    class="text-xs text-zinc-500">{{ $equiposTotal ? '100%' : 'Sin datos' }}</span>
                                            </div>
                                            @foreach ($estadoCards as $card)
                                                @php
                                                    $valorEstado = $card['valor'];
                                                    $porcentaje =
                                                        $equiposTotal > 0
                                                            ? round(($valorEstado / $equiposTotal) * 100)
                                                            : 0;
                                                @endphp
                                                <div>
                                                    <div
                                                        class="flex items-center justify-between text-sm font-semibold text-zinc-600 dark:text-zinc-300">
                                                        <span>{{ $card['label'] }}</span>
                                                        <span>{{ number_format($valorEstado) }}
                                                            ({{ $porcentaje }}%)
                                                        </span>
                                                    </div>
                                                    <div class="mt-2 h-2 rounded-full bg-zinc-100 dark:bg-zinc-800">
                                                        <div class="h-2 rounded-full bg-{{ $card['color'] }}-500"
                                                            style="width: {{ $porcentaje }}%"></div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </section>
                @endif

                @if ($mostrarSeccion('clients'))
                    <section data-section="clients" class="space-y-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-teal-600">Sección</p>
                            <h2 class="text-2xl font-bold text-white">Clientes</h2>
                            <p class="text-sm text-zinc-100 dark:text-zinc-400">Resumen de clientes y sus centros
                                médicos.</p>
                        </div>
                        <div class="grid gap-4 lg:grid-cols-3">
                            <div class="glass-card p-6">
                                <p class="text-sm font-semibold text-teal-600">Clientes registrados</p>
                                <h2 class="text-3xl font-bold text-zinc-900 dark:text-white">
                                    {{ number_format($clientesTotal) }}
                                </h2>
                                <p class="text-sm text-zinc-500 dark:text-zinc-400">Clientes registrados en la
                                    plataforma.</p>
                                <div class="mt-4 flex items-center gap-4 text-sm">
                                    <div>
                                        <p class="text-xs uppercase text-zinc-500">Con centros activos</p>
                                        <p class="text-xl font-semibold text-zinc-900 dark:text-white">
                                            {{ number_format($clientesConCentros) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs uppercase text-zinc-500">Sin centros</p>
                                        <p class="text-xl font-semibold text-zinc-900 dark:text-white">
                                            {{ number_format(max($clientesTotal - $clientesConCentros, 0)) }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="glass-card p-6 lg:col-span-2">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-50">Clientes con más
                                        centros
                                    </h3>
                                    <a href="{{ route('clientes.index') }}"
                                        class="text-sm font-semibold text-blue-600 hover:text-blue-500">Ver
                                        clientes</a>
                                </div>
                                <table class="mt-4 w-full text-left text-sm">
                                    <thead class="text-xs uppercase tracking-wide text-zinc-500">
                                        <tr>
                                            <th class="pb-2">Cliente</th>
                                            <th class="pb-2">Centros</th>
                                            <th class="pb-2">Cantidad</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                                        @forelse ($topClientes as $cliente)
                                            @php
                                                $centros = $cliente->centros_medicos_count ?? 0;
                                                $priority =
                                                    $centros >= 5
                                                        ? 'Alta'
                                                        : ($centros >= 3
                                                            ? 'Media'
                                                            : ($centros <= 1
                                                                ? 'Bajo'
                                                                : 'Normal'));
                                            @endphp
                                            <tr class="text-zinc-700 dark:text-zinc-200">
                                                <td class="py-3 font-semibold">{{ $cliente->nombre }}</td>
                                                <td class="py-3">{{ number_format($centros) }}</td>
                                                <td class="py-3">
                                                    <span
                                                        class="rounded-full bg-zinc-100 px-3 py-0.5 text-xs font-semibold text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">{{ $priority }}</span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="py-3 text-center text-zinc-500">No hay datos
                                                    disponibles.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </section>
                @endif

                @if ($mostrarSeccion('spares'))
                    <section data-section="spares" class="space-y-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-amber-600">Sección</p>
                            <h2 class="text-2xl font-bold text-white">Repuestos</h2>
                            <p class="text-sm text-zinc-100 dark:text-zinc-400">Inventario y alertas de stock crítico.
                            </p>
                        </div>
                        <div class="grid gap-4 lg:grid-cols-3">
                            <div class="glass-card p-6">
                                <p class="text-sm font-semibold text-amber-600">Repuestos registrados</p>
                                <h2 class="text-3xl font-bold text-zinc-900 dark:text-white">
                                    {{ number_format($repuestosTotal) }}
                                </h2>
                                <p class="text-sm text-zinc-500 dark:text-zinc-400">Stock total disponible: <span
                                        class="font-semibold text-zinc-800 dark:text-zinc-200">{{ number_format($repuestosStock) }}</span>
                                    unidades.</p>
                                <dl class="mt-4 space-y-2 text-sm text-zinc-600 dark:text-zinc-300">
                                    <div class="flex items-center justify-between">
                                        <dt>Stock crítico (≤2)</dt>
                                        <dd class="font-semibold text-red-500">{{ number_format($repuestosCriticos) }}
                                        </dd>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <dt>Stock bajo (3 a 5)</dt>
                                        <dd class="font-semibold text-amber-500">{{ number_format($repuestosBajos) }}
                                        </dd>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <dt>Sin stock</dt>
                                        <dd class="font-semibold text-zinc-500">
                                            {{ number_format($repuestosSinStock) }}</dd>
                                    </div>
                                </dl>
                            </div>

                            <div class="glass-card p-6 lg:col-span-2">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-50">Repuestos
                                        próximos a
                                        agotar
                                        stock
                                    </h3>
                                    <a href="{{ route('repuestos.index') }}"
                                        class="text-sm font-semibold text-amber-600 hover:text-amber-500">Ir al
                                        inventario</a>
                                </div>
                                <div class="mt-4 space-y-3">
                                    @forelse ($repuestosPorSalir as $repuesto)
                                        <div
                                            class="flex items-center justify-between rounded-xl border border-zinc-100 px-4 py-3 text-sm dark:border-zinc-800">
                                            <div>
                                                <p class="font-semibold text-zinc-800 dark:text-zinc-100">
                                                    {{ $repuesto->nombre }}
                                                </p>
                                                <p class="text-xs text-zinc-500">ID #{{ $repuesto->id }}</p>
                                            </div>
                                            <span
                                                class="rounded-full bg-amber-50 px-3 py-1 text-xsfont-semibold text-amber-700 dark:bg-amber-900/30 dark:text-amber-200">{{ number_format($repuesto->stock ?? 0) }}
                                                u.</span>
                                        </div>
                                    @empty
                                        <p class="text-sm text-zinc-500">Todos los repuestos cuentan con stock
                                            suficiente.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <div class="glass-card p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-amber-600">Consumo</p>
                                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Repuestos más
                                        usados</h3>
                                </div>
                                <span class="text-xs text-zinc-500">Top 7</span>
                            </div>
                            @if (count($chartRepuestos['labels']))
                                <div class="mt-4">
                                    <canvas id="chart-repuestos" height="220"></canvas>
                                </div>
                            @else
                                <p class="mt-4 text-sm text-zinc-500">No hay uso de repuestos registrado en informes
                                    correctivos.
                                </p>
                            @endif
                        </div>
                    </section>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            (function() {
                const filterForm = document.getElementById('dashboard-filters');
                if (filterForm) {
                    let timeoutId;
                    const scheduleSubmit = () => {
                        clearTimeout(timeoutId);
                        timeoutId = setTimeout(() => filterForm.requestSubmit(), 250);
                    };

                    filterForm.querySelectorAll('select, input[type="checkbox"]').forEach((input) => {
                        input.addEventListener('change', scheduleSubmit);
                    });
                }

                const buildChartConfigs = () => ({
                    fallas: {
                        el: document.getElementById('chart-fallas'),
                        data: {
                            labels: @json($chartFallasData['labels']),
                            datasets: [{
                                label: 'Cantidad',
                                data: @json($chartFallasData['values']),
                                backgroundColor: '#7c3aed',
                                borderRadius: 12,
                                barThickness: 18,
                            }],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                },
                            },
                            scales: {
                                x: {
                                    grid: {
                                        display: false
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        precision: 0
                                    },
                                },
                            },
                        },
                        type: 'bar',
                    },
                    equiposEstado: {
                        el: document.getElementById('chart-equipos-estado'),
                        data: {
                            labels: @json($chartEquiposEstado['labels']),
                            datasets: [{
                                label: 'Equipos',
                                data: @json($chartEquiposEstado['values']),
                                backgroundColor: @json($chartEquiposEstado['colors']),
                                borderRadius: 12,
                                barThickness: 22,
                            }],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false,
                                },
                            },
                            scales: {
                                x: {
                                    grid: {
                                        display: false,
                                    },
                                },
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        precision: 0,
                                    },
                                },
                            },
                        },
                        type: 'bar',
                    },
                    informesMes: {
                        el: document.getElementById('chart-informes-mes'),
                        data: {
                            labels: @json($chartInformesMes['labels']),
                            datasets: [{
                                    label: 'Correctivos',
                                    data: @json($chartInformesMes['correctivos']),
                                    borderColor: '#7c3aed',
                                    backgroundColor: 'rgba(124, 58, 237, 0.2)',
                                    tension: 0.3,
                                    fill: true,
                                },
                                {
                                    label: 'Preventivos',
                                    data: @json($chartInformesMes['preventivos']),
                                    borderColor: '#10b981',
                                    backgroundColor: 'rgba(16, 185, 129, 0.2)',
                                    tension: 0.3,
                                    fill: true,
                                },
                            ],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: {
                                intersect: false,
                                mode: 'index'
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        precision: 0
                                    }
                                },
                            },
                        },
                        type: 'line',
                    },
                    repuestos: {
                        el: document.getElementById('chart-repuestos'),
                        data: {
                            labels: @json($chartRepuestos['labels']),
                            datasets: [{
                                label: 'Unidades usadas',
                                data: @json($chartRepuestos['values']),
                                backgroundColor: '#f59e0b',
                                borderRadius: 12,
                                barThickness: 18,
                            }],
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                x: {
                                    beginAtZero: true,
                                    ticks: {
                                        precision: 0
                                    }
                                },
                                y: {
                                    grid: {
                                        display: false
                                    }
                                },
                            },
                        },
                        type: 'bar',
                    },
                });

                const renderDashboardCharts = () => {
                    if (!window.Chart) {
                        setTimeout(renderDashboardCharts, 200);
                        return;
                    }

                    if (Array.isArray(window.__dashboardCharts)) {
                        window.__dashboardCharts.forEach((chart) => chart.destroy());
                    }
                    window.__dashboardCharts = [];

                    Object.values(buildChartConfigs()).forEach((config) => {
                        if (config.el && config.data.labels.length > 0) {
                            const instance = new window.Chart(config.el.getContext('2d'), {
                                type: config.type,
                                data: config.data,
                                options: config.options,
                            });

                            window.__dashboardCharts.push(instance);
                        }
                    });
                };

                const bootDashboardCharts = () => {
                    if (document.readyState === 'loading') {
                        document.addEventListener('DOMContentLoaded', renderDashboardCharts, {
                            once: true,
                        });
                    } else {
                        renderDashboardCharts();
                    }
                };

                bootDashboardCharts();
                document.addEventListener('livewire:navigated', renderDashboardCharts);
            })();
        </script>
    @endpush
</x-layouts.app>
