<x-layouts.app :title="__('Dashboard')">
    @push('styles')
        <style>
            .dashboard-shell {
                border-radius: 36px;
                padding: clamp(1.25rem, 4vw, 2.75rem);
                background: radial-gradient(circle at top, rgba(59, 130, 246, 0.35), transparent 55%),
                    linear-gradient(135deg, rgba(15, 23, 42, 0.95), rgba(24, 24, 27, 0.75));
                border: 1px solid rgba(255, 255, 255, 0.08);
                box-shadow: 0 35px 80px rgba(3, 7, 18, 0.55);
            }

            .dark .dashboard-shell {
                background: radial-gradient(circle at 20% 0%, rgba(14, 165, 233, 0.25), transparent 60%),
                    linear-gradient(135deg, rgba(9, 9, 11, 0.95), rgba(24, 24, 27, 0.85));
                border-color: rgba(255, 255, 255, 0.06);
            }

            .glass-card {
                border-radius: 24px;
                border: 1px solid rgba(255, 255, 255, 0.18);
                background: rgba(255, 255, 255, 0.75);
                box-shadow: 0 20px 60px rgba(15, 23, 42, 0.25);
                backdrop-filter: blur(18px);
                padding: clamp(1.5rem, 3vw, 2.25rem);
            }

            .dark .glass-card {
                background: rgba(12, 12, 15, 0.65);
                border-color: rgba(148, 163, 184, 0.25);
                box-shadow: 0 25px 65px rgba(0, 0, 0, 0.55);
            }

            .glass-card--accent {
                background: linear-gradient(135deg, rgba(59, 130, 246, 0.95), rgba(14, 165, 233, 0.9));
                color: #fff;
                border-color: rgba(255, 255, 255, 0.35);
                box-shadow: 0 30px 80px rgba(14, 165, 233, 0.4);
                padding: clamp(1.75rem, 3.5vw, 2.5rem);
            }

            .glass-card--muted {
                background: rgba(248, 250, 252, 0.85);
                border-color: rgba(226, 232, 240, 0.55);
                color: #0f172a;
            }

            .dark .glass-card--muted {
                background: rgba(15, 23, 42, 0.7);
                border-color: rgba(71, 85, 105, 0.6);
                color: #e2e8f0;
            }

            .glass-card--state {
                transition: transform 180ms ease, box-shadow 180ms ease;
            }

            .glass-card--state:hover {
                transform: translateY(-4px);
                box-shadow: 0 18px 45px rgba(15, 23, 42, 0.35);
            }

            .glass-pill {
                border-radius: 999px;
                padding: 0.3rem 0.95rem;
                font-size: 0.75rem;
                font-weight: 600;
                border: 1px solid rgba(255, 255, 255, 0.25);
                background: rgba(255, 255, 255, 0.2);
                color: inherit;
            }

            .dark .glass-pill {
                background: rgba(255, 255, 255, 0.08);
                border-color: rgba(255, 255, 255, 0.16);
                color: #e2e8f0;
            }
        </style>
    @endpush

    @php
        $rangeOptions = [
            7 => 'Últimos 7 días',
            30 => 'Últimos 30 días',
            90 => 'Últimos 90 días',
            180 => 'Últimos 180 días',
            365 => 'Últimos 12 meses',
        ];

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

    @endphp

    <div class="dashboard-shell flex flex-col gap-6">
        <div class="glass-card p-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wide text-blue-600">Panel personalizable</p>
                    <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-50">Visión general de operaciones</h1>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">Controla equipos, informes, clientes y repuestos
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
                                <option value="{{ $value }}" @selected($rangoSeleccionado === $value)>{{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <span class="text-sm font-medium text-zinc-600 dark:text-zinc-300">Estados incluidos</span>
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

            <div class="mt-6 border-t border-dashed border-zinc-200 pt-6 dark:border-zinc-700">
                <p class="text-sm font-semibold text-zinc-600 dark:text-zinc-300">Elige qué módulos mostrar</p>
                <div class="mt-3 flex flex-wrap gap-4 text-sm font-medium text-zinc-600 dark:text-zinc-300">
                    @foreach ([
        'machines' => 'Máquinas',
        'informes' => 'Informes',
        'clients' => 'Clientes',
        'spares' => 'Repuestos',
    ] as $section => $label)
                        <label
                            class="inline-flex items-center gap-2 rounded-full border border-zinc-200 px-3 py-1 text-xs font-semibold transition hover:border-blue-400 hover:text-blue-600 dark:border-zinc-700">
                            <input type="checkbox" value="{{ $section }}" data-section-toggle
                                class="size-4 rounded border-zinc-300 text-blue-600 focus:ring-blue-500 dark:border-zinc-600"
                                checked>
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <section data-section="machines" class="space-y-4">
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <div class="glass-card glass-card--accent p-5">
                    <div class="flex items-center gap-3">
                        <div class="rounded-2xl bg-white/20 p-3 text-white shadow-lg shadow-blue-900/30 backdrop-blur">
                            <i class="fa-solid fa-screwdriver-wrench text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-white/90">Total de máquinas</p>
                            <p class="text-3xl font-black">
                                {{ number_format($equiposTotal) }}</p>
                        </div>
                    </div>
                    <p class="mt-3 text-xs text-white/80">Estados monitoreados: Operativo, En observación, Fuera de
                        servicio y Baja.</p>
                </div>

                @foreach ($estadoCards as $card)
                    @php
                        $porcentaje = $equiposTotal > 0 ? round(($card['valor'] / $equiposTotal) * 100) : 0;
                        $resaltado = in_array($card['key'], $estadosSeleccionados, true);
                    @endphp
                    <div class="glass-card glass-card--state p-5 {{ $resaltado ? 'ring-2 ring-blue-400' : '' }}">
                        <div class="flex items-center justify-between">
                            <div>
                                <p
                                    class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                                    {{ $card['label'] }}</p>
                                <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-50">
                                    {{ number_format($card['valor']) }}</p>
                            </div>
                            <span class="glass-pill text-{{ $card['color'] }}-700 dark:text-{{ $card['color'] }}-200">
                                {{ $porcentaje }}%
                            </span>
                        </div>
                        <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">{{ $card['descripcion'] }}</p>
                        <div class="mt-4 h-2 w-full rounded-full bg-zinc-100 dark:bg-zinc-800">
                            <div class="h-2 rounded-full bg-gradient-to-r from-{{ $card['color'] }}-400 to-{{ $card['color'] }}-600"
                                style="width: {{ $porcentaje }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="grid gap-4 lg:grid-cols-3">
                <div class="glass-card glass-card--muted">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-50">Distribución por estado</h2>
                        <span class="text-xs text-zinc-500">{{ $equiposTotal ? '100%' : 'Sin datos' }}</span>
                    </div>
                    <div class="mt-4 space-y-4">
                        @foreach ($estadoCards as $card)
                            @php
                                $valorEstado = $card['valor'];
                                $porcentaje = $equiposTotal > 0 ? round(($valorEstado / $equiposTotal) * 100) : 0;
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

                <div class="rounded-2xl border border-zinc-200 p-5 shadow-sm dark:border-zinc-700">
                    <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-50">Equipos críticos recientes</h2>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Monitorea rápidamente las máquinas fuera de
                        servicio o en revisión.</p>
                    <div class="mt-4 space-y-4">
                        @forelse ($equiposCriticos as $equipo)
                            <div class="rounded-xl border border-zinc-100 p-3 text-sm dark:border-zinc-800">
                                <div class="flex items-center justify-between">
                                    <p class="font-semibold text-zinc-800 dark:text-zinc-100">{{ $equipo->nombre }}</p>
                                    <span
                                        class="text-xs text-zinc-500">{{ optional($equipo->updated_at)->diffForHumans() }}</span>
                                </div>
                                <p class="text-xs text-zinc-500">Código: {{ $equipo->codigo ?? 'SD' }}</p>
                                <span
                                    class="mt-2 inline-flex rounded-full bg-zinc-100 px-3 py-0.5 text-xs font-semibold text-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">{{ $equipo->estado }}</span>
                                @if ($equipo->cant_dias_fuera_serv)
                                    <p class="mt-1 text-xs text-zinc-500">{{ $equipo->cant_dias_fuera_serv }} días
                                        fuera de servicio</p>
                                @endif
                            </div>
                        @empty
                            <p class="text-sm text-zinc-500">No hay registros críticos en este momento.</p>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-2xl border border-zinc-200 p-5 shadow-sm dark:border-zinc-700">
                    <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-50">Resumen de actividad</h2>
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
                        <p>Consejo: usa las casillas de "Resaltar estados" para centrar tu atención en los estados
                            críticos.</p>
                    </div>
                </div>
            </div>
        </section>

        <section data-section="informes" class="glass-card p-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wide text-purple-600">Informes técnicos</p>
                    <h2 class="text-xl font-bold text-zinc-900 dark:text-zinc-50">Correctivos y preventivos</h2>
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
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-50">Actividad reciente</h3>
                        <span class="text-sm text-zinc-500">{{ $rangeOptions[$rangoSeleccionado] }}</span>
                    </div>
                    <dl class="mt-4 grid grid-cols-2 gap-4 text-sm">
                        <div class="rounded-xl bg-purple-50 p-4 dark:bg-purple-900/20">
                            <dt class="text-xs uppercase text-purple-600">Correctivos</dt>
                            <dd class="text-2xl font-bold text-purple-800 dark:text-purple-600">
                                {{ number_format($informesPeriodoCorrectivos) }}</dd>
                        </div>
                        <div class="rounded-xl bg-emerald-50 p-4 dark:bg-emerald-900/20">
                            <dt class="text-xs uppercase text-emerald-600">Preventivos</dt>
                            <dd class="text-2xl font-bold text-emerald-800 dark:text-emerald-600">
                                {{ number_format($informesPeriodoPreventivos) }}</dd>
                        </div>
                    </dl>
                    <p class="mt-4 text-xs text-zinc-500">Los valores consideran informes con fecha igual o posterior
                        al {{ $fechaInicio->format('d/m/Y') }}.</p>
                </div>

                <div class="rounded-2xl border border-zinc-200 p-5 dark:border-zinc-700">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-50">Próximos pasos</h3>
                    <ul class="mt-4 space-y-3 text-sm text-zinc-600 dark:text-zinc-300">
                        <li class="flex items-center gap-3">
                            <i class="fa-solid fa-file-circle-plus text-purple-500"></i>
                            <span>Planifica informes preventivos para los equipos en revisión.</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fa-solid fa-turn-up text-emerald-500"></i>
                            <span>Convierte correctivos recurrentes en mantenimientos programados.</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fa-solid fa-diagram-project text-blue-500"></i>
                            <span>Comparte el resumen PDF con clientes clave desde la sección de informes.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </section>

        <section data-section="clients" class="grid gap-4 lg:grid-cols-3">
            <div class="glass-card p-6">
                <p class="text-sm font-semibold text-teal-600">Clientes</p>
                <h2 class="text-3xl font-bold text-zinc-900 dark:text-white">{{ number_format($clientesTotal) }}</h2>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">Clientes registrados en la plataforma.</p>
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
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-50">Clientes con más centros</h3>
                    <a href="{{ route('clientes.index') }}"
                        class="text-sm font-semibold text-blue-600 hover:text-blue-500">Ver clientes</a>
                </div>
                <table class="mt-4 w-full text-left text-sm">
                    <thead class="text-xs uppercase tracking-wide text-zinc-500">
                        <tr>
                            <th class="pb-2">Cliente</th>
                            <th class="pb-2">Centros</th>
                            <th class="pb-2">Prioridad</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @forelse ($topClientes as $cliente)
                            @php
                                $centros = $cliente->centros_medicos_count ?? 0;
                                $priority = $centros >= 5 ? 'Alta' : ($centros >= 3 ? 'Media' : 'Normal');
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
                                <td colspan="3" class="py-3 text-center text-zinc-500">No hay datos disponibles.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section data-section="spares" class="grid gap-4 lg:grid-cols-3">
            <div class="glass-card p-6">
                <p class="text-sm font-semibold text-amber-600">Repuestos registrados</p>
                <h2 class="text-3xl font-bold text-zinc-900 dark:text-white">{{ number_format($repuestosTotal) }}</h2>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">Stock total disponible: <span
                        class="font-semibold text-zinc-800 dark:text-zinc-200">{{ number_format($repuestosStock) }}</span>
                    unidades.</p>
                <dl class="mt-4 space-y-2 text-sm text-zinc-600 dark:text-zinc-300">
                    <div class="flex items-center justify-between">
                        <dt>Stock crítico (≤2)</dt>
                        <dd class="font-semibold text-red-500">{{ number_format($repuestosCriticos) }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt>Stock bajo (3 a 5)</dt>
                        <dd class="font-semibold text-amber-500">{{ number_format($repuestosBajos) }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt>Sin stock</dt>
                        <dd class="font-semibold text-zinc-500">{{ number_format($repuestosSinStock) }}</dd>
                    </div>
                </dl>
            </div>

            <div class="glass-card p-6 lg:col-span-2">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-50">Repuestos próximos a agotar stock
                    </h3>
                    <a href="{{ route('repuestos.index') }}"
                        class="text-sm font-semibold text-amber-600 hover:text-amber-500">Ir al inventario</a>
                </div>
                <div class="mt-4 space-y-3">
                    @forelse ($repuestosPorSalir as $repuesto)
                        <div
                            class="flex items-center justify-between rounded-xl border border-zinc-100 px-4 py-3 text-sm dark:border-zinc-800">
                            <div>
                                <p class="font-semibold text-zinc-800 dark:text-zinc-100">{{ $repuesto->nombre }}</p>
                                <p class="text-xs text-zinc-500">ID #{{ $repuesto->id }}</p>
                            </div>
                            <span
                                class="rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-900/30 dark:text-amber-200">{{ number_format($repuesto->stock ?? 0) }}
                                u.</span>
                        </div>
                    @empty
                        <p class="text-sm text-zinc-500">Todos los repuestos cuentan con stock suficiente.</p>
                    @endforelse
                </div>
            </div>
        </section>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
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

                const STORAGE_KEY = 'tqmd-dashboard-sections';
                const defaultState = {
                    machines: true,
                    informes: true,
                    clients: true,
                    spares: true,
                };

                let storedState = {};
                try {
                    storedState = JSON.parse(localStorage.getItem(STORAGE_KEY) ?? '{}');
                } catch (error) {
                    storedState = {};
                }

                const state = {
                    ...defaultState,
                    ...storedState,
                };

                const applyState = () => {
                    document.querySelectorAll('[data-section]').forEach((section) => {
                        const key = section.getAttribute('data-section');
                        const visible = state[key] ?? true;
                        section.classList.toggle('hidden', !visible);
                    });

                    document.querySelectorAll('[data-section-toggle]').forEach((checkbox) => {
                        const key = checkbox.value;
                        checkbox.checked = state[key] ?? true;
                    });
                };

                document.querySelectorAll('[data-section-toggle]').forEach((checkbox) => {
                    checkbox.addEventListener('change', () => {
                        state[checkbox.value] = checkbox.checked;
                        localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
                        applyState();
                    });
                });

                applyState();
            });
        </script>
    @endpush
</x-layouts.app>
