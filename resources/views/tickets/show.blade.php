<x-layouts.app :title="$title ?? 'Detalle Ticket'">
    <div class="min-h-screen px-4 py-8 bg-zinc-50 dark:bg-zinc-900 transition-all">
        <div class="max-w-7xl mx-auto">
            {{-- Header --}}
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-zinc-800 dark:text-white">Ticket #{{ $ticket->numero_ticket }}
                    </h1>
                    <p class="text-zinc-600 dark:text-zinc-400 mt-1">Detalles completos del ticket de soporte</p>
                </div>
                <a href="{{ route('tickets.index') }}">
                    <button type="button"
                        class="bg-zinc-600 dark:bg-zinc-700 text-white dark:text-zinc-100 font-semibold px-4 py-2 rounded-lg hover:bg-zinc-700 dark:hover:bg-zinc-600 transition flex items-center gap-2">
                        <i class="fa fa-arrow-left"></i>
                        Volver
                    </button>
                </a>
            </div>

            {{-- Fila 1: Información del Ticket + Detalles | Información del Cliente --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                {{-- Información del Ticket con Detalles --}}
                <div
                    class="bg-white dark:bg-zinc-800 rounded-lg shadow-md p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-5">
                        <h2 class="text-xl font-bold text-zinc-800 dark:text-white flex items-center gap-2">
                            <i class="fa fa-ticket text-blue-500"></i>
                            Información del Ticket
                        </h2>
                        @php
                            $estadoConfig = [
                                'pendiente' => [
                                    'class' =>
                                        'bg-yellow-100 dark:bg-yellow-900/40 text-yellow-800 dark:text-yellow-200',
                                    'text' => 'Pendiente',
                                    'icon' => 'fa-clock',
                                ],
                                'en_proceso' => [
                                    'class' => 'bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-200',
                                    'text' => 'En Proceso',
                                    'icon' => 'fa-spinner',
                                ],
                                'completado' => [
                                    'class' => 'bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-200',
                                    'text' => 'Completado',
                                    'icon' => 'fa-check-circle',
                                ],
                            ];
                            $config = $estadoConfig[$ticket->estado] ?? [
                                'class' => 'bg-zinc-100 dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200',
                                'text' => $ticket->estado,
                                'icon' => 'fa-circle',
                            ];
                        @endphp
                        <span
                            class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-semibold transition-colors duration-200 {{ $config['class'] }}">
                            <i class="fa {{ $config['icon'] }}"></i>
                            {{ $config['text'] }}
                        </span>
                    </div>

                    {{-- Información del Ticket --}}
                    <div class="space-y-2">
                        <div class="bg-zinc-100 dark:bg-zinc-700/50 p-3 rounded-lg">
                            <span
                                class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wide block mb-1">Número
                                de Ticket</span>
                            <p class="text-lg font-bold text-zinc-900 dark:text-zinc-100">{{ $ticket->numero_ticket }}
                            </p>
                        </div>

                        <div class="bg-zinc-100 dark:bg-zinc-700/50 p-3 rounded-lg">
                            <span
                                class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wide block mb-1">Fecha
                                de Creación</span>
                            <p class="text-base font-semibold text-zinc-900 dark:text-zinc-100">
                                {{ $ticket->created_at->format('d/m/Y H:i') }}</p>
                        </div>

                        <div class="bg-zinc-100 dark:bg-zinc-700/50 p-3 rounded-lg">
                            <span
                                class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wide block mb-1">Momento
                                de la Falla</span>
                            <p class="text-base font-semibold text-zinc-900 dark:text-zinc-100">
                                {{ $ticket->momento_falla }}</p>
                            @if ($ticket->momento_falla === 'Otras' && $ticket->momento_falla_otras)
                                <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">
                                    {{ $ticket->momento_falla_otras }}</p>
                            @endif
                        </div>

                        <div class="bg-zinc-100 dark:bg-zinc-700/50 p-3 rounded-lg">
                            <span
                                class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wide block mb-1">Fecha
                                de Visita</span>
                            <p class="text-base font-semibold text-zinc-900 dark:text-zinc-100">
                                {{ $ticket->fecha_visita ? $ticket->fecha_visita->format('d/m/Y H:i') : 'No programada' }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Información del Cliente --}}
                <div
                    class="bg-white dark:bg-zinc-800 rounded-lg shadow-md p-6 border border-zinc-200 dark:border-zinc-700">
                    <h2 class="text-xl font-bold text-zinc-800 dark:text-white mb-5 flex items-center gap-2">
                        <i class="fa fa-user text-blue-500"></i>
                        Información del Cliente
                    </h2>

                    <div class="space-y-2">
                        <div class="bg-zinc-100 dark:bg-zinc-700/50 p-3 rounded-lg">
                            <span
                                class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wide block mb-1">Cliente</span>
                            <p class="text-lg font-bold text-zinc-900 dark:text-zinc-100">{{ $ticket->cliente }}</p>
                        </div>

                        <div class="bg-zinc-100 dark:bg-zinc-700/50 p-3 rounded-lg">
                            <span
                                class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wide block mb-1">Solicitante</span>
                            <p class="text-base font-semibold text-zinc-900 dark:text-zinc-100">
                                {{ $ticket->nombre_apellido }}</p>
                        </div>

                        <div class="bg-zinc-100 dark:bg-zinc-700/50 p-3 rounded-lg">
                            <span
                                class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wide block mb-1">Cargo</span>
                            <p class="text-base font-semibold text-zinc-900 dark:text-zinc-100">{{ $ticket->cargo }}
                            </p>
                        </div>

                        <div class="bg-zinc-100 dark:bg-zinc-700/50 p-3 rounded-lg">
                            <span
                                class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wide block mb-1">Teléfono</span>
                            <p class="text-base font-semibold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                                <i class="fa fa-phone text-zinc-500 dark:text-zinc-400"></i>
                                {{ $ticket->telefono }}
                            </p>
                        </div>

                        <div class="bg-zinc-100 dark:bg-zinc-700/50 p-3 rounded-lg">
                            <span
                                class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wide block mb-1">Email</span>
                            <p class="text-base font-semibold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                                <i class="fa fa-envelope text-zinc-500 dark:text-zinc-400"></i>
                                {{ $ticket->email ?: 'No registrado' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Información del Equipo --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <div
                    class="bg-white dark:bg-zinc-800 rounded-lg shadow-md p-6 border border-zinc-200 dark:border-zinc-700">
                    <h2 class="text-xl font-bold text-zinc-800 dark:text-white mb-5 flex items-center gap-2">
                        <i class="fa fa-desktop text-blue-500"></i>
                        Información del Equipo
                    </h2>

                    <div class="space-y-2">
                        <div class="bg-zinc-100 dark:bg-zinc-700/50 p-4 rounded-lg">
                            <span
                                class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wide block mb-1">ID/Nº
                                Equipo</span>
                            <p class="text-base font-semibold text-zinc-900 dark:text-zinc-100">
                                {{ $ticket->id_numero_equipo ?: 'No especificado' }}</p>
                        </div>

                        <div class="bg-zinc-100 dark:bg-zinc-700/50 p-4 rounded-lg">
                            <span
                                class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wide block mb-1">Modelo
                                de Máquina</span>
                            <p class="text-base font-semibold text-zinc-900 dark:text-zinc-100">
                                {{ $ticket->modelo_maquina ?: 'No especificado' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Falla presentada y Acciones realizadas en una sola tarjeta --}}
                <div
                    class="bg-white dark:bg-zinc-800 rounded-lg shadow-md p-6 border border-zinc-200 dark:border-zinc-700">
                    <h2 class="text-xl font-bold text-zinc-800 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fa fa-exclamation-triangle text-red-500"></i>
                        Falla Presentada
                    </h2>
                    <div class="bg-zinc-100 dark:bg-zinc-700/50 p-4 rounded-lg mb-6">
                        <p class="text-zinc-900 dark:text-zinc-100 leading-relaxed whitespace-pre-line">
                            {{ $ticket->falla_presentada }}</p>
                    </div>

                    {{-- Acciones realizadas en la misma tarjeta --}}
                    <h3 class="text-xl font-bold text-zinc-800 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fa fa-wrench text-green-500"></i>
                        Acciones Realizadas por el cliente
                    </h3>
                    <div class="bg-zinc-100 dark:bg-zinc-700/50 p-4 rounded-lg">
                        <p class="text-zinc-900 dark:text-zinc-100 leading-relaxed whitespace-pre-line">
                            {{ $ticket->acciones_realizadas ?: 'No se han registrado acciones' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Fila 3: Técnico Asignado | Acciones realizadas por el Técnico --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                {{-- Técnico Asignado --}}
                <div
                    class="bg-white dark:bg-zinc-800 rounded-lg shadow-md p-6 border border-zinc-200 dark:border-zinc-700">
                    <h2 class="text-xl font-bold text-zinc-800 dark:text-white mb-5 flex items-center gap-2">
                        <i class="fa fa-user-md text-blue-500"></i>
                        Técnico Asignado
                    </h2>

                    <div class="bg-zinc-100 dark:bg-zinc-700/50 p-6 rounded-lg text-center">
                        <div
                            class="w-20 h-20 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full mx-auto mb-4 flex items-center justify-center">
                            <i class="fa fa-user text-3xl text-white"></i>
                        </div>
                        <p class="text-xl font-bold text-zinc-900 dark:text-zinc-100 mb-1">
                            {{ $ticket->tecnicoAsignado->name ?? 'Sin asignar' }}
                        </p>
                        @if ($ticket->tecnicoAsignado)
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                {{ $ticket->tecnicoAsignado->email ?? '' }}
                            </p>
                        @else
                            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-2">
                                <i class="fa fa-info-circle"></i>
                                No hay técnico asignado aún
                            </p>
                        @endif
                    </div>
                </div>

                {{-- Acciones realizadas por el Técnico (Placeholder) --}}
                <div
                    class="bg-white dark:bg-zinc-800 rounded-lg shadow-md p-6 border border-zinc-200 dark:border-zinc-700">
                    <h2 class="text-xl font-bold text-zinc-800 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fa fa-tasks text-purple-500"></i>
                        Acciones realizadas por el Técnico
                    </h2>
                    <div class="bg-zinc-100 dark:bg-zinc-700/50 p-8 rounded-lg text-center">
                        <i class="fa fa-clipboard text-4xl text-zinc-400 dark:text-zinc-500 mb-3"></i>
                        <p class="text-zinc-600 dark:text-zinc-400 font-medium">Sin acciones registradas</p>
                        <p class="text-xs text-zinc-500 dark:text-zinc-500 mt-2">El técnico aún no ha reportado
                            acciones</p>
                    </div>
                </div>
            </div>

            {{-- Fila 4: Historial del Ticket (Ancho completo) --}}
            <div class="mb-6">
                <div
                    class="bg-white dark:bg-zinc-800 rounded-lg shadow-md p-6 border border-zinc-200 dark:border-zinc-700">
                    <h2 class="text-xl font-bold text-zinc-800 dark:text-white mb-6 flex items-center gap-2">
                        <i class="fa fa-history text-purple-500"></i>
                        Historial del Ticket
                    </h2>

                    {{-- Timeline Horizontal de Progreso --}}
                    @php
                        $estadoActual = $ticket->estado;
                        $fechaVisita = $ticket->fecha_visita;

                        // Determinar estados completados
                        $estados = [
                            'creado' => true, // Siempre está creado
                            'en_proceso' => in_array($estadoActual, ['en_proceso', 'completado']),
                            'visita' => $fechaVisita !== null,
                            'completado' => $estadoActual === 'completado',
                        ];

                        // Detectar si hay reagendación
                        $reagendado = $ticket->historial->where('accion', 'like', '%reagend%')->count() > 0;

                        // Obtener fechas de eventos
                        $enProcesoEvento = $ticket->historial->where('estado_nuevo', 'en_proceso')->first();
                        $completadoEvento = $ticket->historial->where('estado_nuevo', 'completado')->last();

                        // Verificar si el ticket está completado pero no hay evento en historial
                        if ($estadoActual === 'completado' && !$completadoEvento) {
                            $completadoEvento = (object) ['fecha' => $ticket->updated_at];
                        }
                    @endphp

                    <div class="relative px-5">
                        {{-- Línea de tiempo mejorada --}}
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-6">Estado del
                            Ticket</h3>

                        <div class="relative">
                            {{-- Línea de progreso base --}}
                            <div class="absolute top-6 left-0 right-0 h-0.5 bg-gray-200 dark:bg-gray-600 mx-6">
                            </div>

                            {{-- Línea de progreso activa --}}
                            <div class="absolute top-6 left-6 h-0.5 bg-gradient-to-r from-blue-500 to-blue-600 dark:from-blue-400 dark:to-blue-500 transition-all duration-700 ease-in-out z-10"
                                style="width: calc({{ $estados['completado'] ? '100%' : ($estados['visita'] ? '66.66%' : ($estados['en_proceso'] ? '33.33%' : '0%')) }} - 48px);">
                            </div>

                            {{-- Pasos de la línea de tiempo --}}
                            <div class="relative flex justify-between items-start">
                                {{-- 1. Creado --}}
                                <div class="flex flex-col items-center group">
                                    <div class="relative">
                                        <div
                                            class="w-10 h-10 rounded-full flex items-center justify-center shadow-lg transition-all duration-300 transform group-hover:scale-110 bg-blue-500 dark:bg-blue-400 z-30 relative">
                                            <div
                                                class="absolute inset-0 rounded-full bg-blue-500 dark:bg-blue-400 animate-pulse opacity-75">
                                            </div>
                                            <i class="fa fa-check text-white text-sm relative z-10"></i>
                                        </div>
                                        {{-- Indicador de estado activo --}}
                                        <div
                                            class="absolute -top-1 -right-1 w-4 h-4 bg-green-400 dark:bg-green-300 rounded-full border-2 border-white dark:border-gray-800 z-30">
                                        </div>
                                    </div>
                                    <div class="mt-3 text-center max-w-20">
                                        <span
                                            class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Creado</span>
                                        <span
                                            class="block text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $ticket->created_at->format('d/m/Y') }}</span>
                                    </div>
                                </div>

                                {{-- 2. En Proceso --}}
                                <div class="flex flex-col items-center group">
                                    <div class="relative">
                                        <div
                                            class="w-10 h-10 rounded-full flex items-center justify-center shadow-lg transition-all duration-300 transform group-hover:scale-110 z-20 relative
                        {{ $estados['en_proceso'] ? 'bg-blue-500 dark:bg-blue-400' : 'bg-gray-300 dark:bg-gray-600' }}">
                                            @if ($estados['completado'])
                                                <i class="fa fa-check text-white text-sm"></i>
                                            @elseif($estados['en_proceso'])
                                                <div class="relative">
                                                    <i class="fa fa-cog fa-spin text-white text-sm"></i>
                                                    <div
                                                        class="absolute inset-0 rounded-full bg-blue-500 dark:bg-blue-400 animate-pulse opacity-50">
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-gray-600 dark:text-gray-300 font-bold">2</span>
                                            @endif
                                        </div>
                                        {{-- Indicador de estado --}}
                                        @if ($estados['en_proceso'] && !$estados['completado'])
                                            <div
                                                class="absolute -top-1 -right-1 w-4 h-4 bg-yellow-400 dark:bg-yellow-300 rounded-full border-2 border-white dark:border-gray-800 z-30 animate-pulse">
                                            </div>
                                        @elseif($estados['completado'])
                                            <div
                                                class="absolute -top-1 -right-1 w-4 h-4 bg-green-400 dark:bg-green-300 rounded-full border-2 border-white dark:border-gray-800 z-30">
                                            </div>
                                        @endif
                                    </div>
                                    <div class="mt-3 text-center max-w-20">
                                        <span class="block text-sm font-semibold text-gray-700 dark:text-gray-300">En
                                            Proceso</span>
                                        @if ($estados['completado'])
                                            <span class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                {{ $enProcesoEvento ? $enProcesoEvento->fecha->format('d/m/Y') : 'Completado' }}
                                            </span>
                                        @elseif($enProcesoEvento)
                                            <span
                                                class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $enProcesoEvento->fecha->format('d/m/Y') }}</span>
                                        @else
                                            <span
                                                class="text-xs text-gray-400 dark:text-gray-500 mt-1">Pendiente</span>
                                        @endif
                                    </div>
                                </div>

                                {{-- 3. Visita Agendada --}}
                                <div class="flex flex-col items-center group">
                                    <div class="relative">
                                        <div
                                            class="w-10 h-10 rounded-full flex items-center justify-center shadow-lg transition-all duration-300 transform group-hover:scale-110 z-20 relative
                        {{ $estados['visita'] ? 'bg-blue-500 dark:bg-blue-400' : 'bg-gray-300 dark:bg-gray-600' }}">
                                            @if ($estados['completado'] || ($estados['visita'] && $estadoActual === 'completado'))
                                                <i class="fa fa-check text-white text-sm"></i>
                                            @elseif($estados['visita'])
                                                <div class="relative">
                                                    <i class="fa fa-calendar text-white text-sm"></i>
                                                    @if ($reagendado)
                                                        <div
                                                            class="absolute -top-0.5 -right-0.5 w-2 h-2 bg-orange-400 rounded-full">
                                                        </div>
                                                    @endif
                                                </div>
                                            @else
                                                <i
                                                    class="fa fa-calendar-o text-gray-600 dark:text-gray-300 text-sm"></i>
                                            @endif
                                        </div>
                                        {{-- Indicador de estado --}}
                                        @if ($estados['visita'] && !$estados['completado'])
                                            <div
                                                class="absolute -top-1 -right-1 w-4 h-4 bg-purple-400 dark:bg-purple-300 rounded-full border-2 border-white dark:border-gray-800 z-30">
                                            </div>
                                        @elseif($estados['completado'])
                                            <div
                                                class="absolute -top-1 -right-1 w-4 h-4 bg-green-400 dark:bg-green-300 rounded-full border-2 border-white dark:border-gray-800 z-30">
                                            </div>
                                        @endif
                                    </div>
                                    <div class="mt-3 text-center max-w-24">
                                        <span class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                            {{ $reagendado ? 'Reagendada' : 'Visita' }}
                                        </span>
                                        @if ($fechaVisita)
                                            <span
                                                class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $fechaVisita->format('d/m H:i') }}</span>
                                        @else
                                            <span class="text-xs text-gray-400 dark:text-gray-500 mt-1">Sin
                                                agendar</span>
                                        @endif
                                    </div>
                                </div>

                                {{-- 4. Completado --}}
                                <div class="flex flex-col items-center group">
                                    <div class="relative">
                                        <div
                                            class="w-10 h-10 rounded-full flex items-center justify-center shadow-lg transition-all duration-300 transform group-hover:scale-110 z-20 relative
                        {{ $estados['completado'] ? 'bg-green-500 dark:bg-green-400' : 'bg-gray-300 dark:bg-gray-600' }}">
                                            @if ($estados['completado'])
                                                <div class="relative">
                                                    <i class="fa fa-check-circle text-white text-lg"></i>
                                                    <div
                                                        class="absolute inset-0 rounded-full animate-pulse opacity-75">
                                                    </div>
                                                </div>
                                            @else
                                                <i class="fa fa-flag-o text-gray-600 dark:text-gray-300 text-sm"></i>
                                            @endif
                                        </div>
                                        {{-- Indicador de completado --}}
                                        @if ($estados['completado'])
                                            <div
                                                class="absolute -top-1 -right-1 w-4 h-4 bg-green-400 dark:bg-green-300 rounded-full border-2 border-white dark:border-gray-800 z-30">
                                                <div
                                                    class="w-full h-full rounded-full bg-green-400 dark:bg-green-300 animate-ping opacity-75">
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="mt-3 text-center max-w-20">
                                        <span
                                            class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Completado</span>
                                        @if ($completadoEvento)
                                            <span
                                                class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $completadoEvento->fecha->format('d/m H:i') }}</span>
                                        @else
                                            <span
                                                class="text-xs text-gray-400 dark:text-gray-500 mt-1">Pendiente</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-zinc-100 dark:bg-zinc-700/50 p-4 rounded-lg"></div>

                    {{-- Timeline Vertical de Eventos --}}
                    @if ($ticket->historial->count() > 0)
                        <h3 class="text-lg font-semibold text-zinc-800 dark:text-white mb-4 flex items-center gap-2">
                            <i class="fa fa-list-alt text-blue-500"></i>
                            Registro de Actividades
                        </h3>

                        <div class="relative">
                            {{-- Línea vertical del timeline --}}
                            <div class="absolute left-5 top-0 bottom-0 w-0.5 bg-zinc-200 dark:bg-zinc-700">
                            </div>

                            <div class="space-y-6">
                                @foreach ($ticket->historial as $evento)
                                    <div class="relative flex gap-4">
                                        {{-- Punto del timeline --}}
                                        <div class="relative z-10 flex-shrink-0 text-zinc-900 dark:text-zinc-100">
                                            @php
                                                $iconConfig = [
                                                    'Creación de ticket' => [
                                                        'icon' => 'fa-plus-circle',
                                                        'class' => 'bg-blue-500',
                                                    ],
                                                    'Actualización de ticket' => [
                                                        'icon' => 'fa-edit',
                                                        'class' => 'bg-yellow-500',
                                                    ],
                                                    'Comentario agregado' => [
                                                        'icon' => 'fa-comment',
                                                        'class' => 'bg-purple-500',
                                                    ],
                                                    'Eliminación de ticket' => [
                                                        'icon' => 'fa-trash',
                                                        'class' => 'bg-red-500',
                                                    ],
                                                ];
                                                $config = $iconConfig[$evento->accion] ?? [
                                                    'icon' => 'fa-circle',
                                                    'class' => 'bg-gray-500',
                                                ];
                                            @endphp
                                            <div
                                                class="w-10 h-10 {{ $config['class'] }} rounded-full flex items-center justify-center text-white shadow-lg border-4 border-white dark:border-zinc-800">
                                                <i class="fa {{ $config['icon'] }} text-sm"></i>
                                            </div>
                                        </div>

                                        {{-- Contenido del evento --}}
                                        <div
                                            class="flex-1 bg-zinc-700/50 rounded-lg p-4 shadow-sm border border-zinc-200 dark:border-zinc-600">
                                            <div class="flex items-start justify-between mb-3 bg-zinc-700/50">
                                                <div>
                                                    <h3 class="font-bold text-zinc-900 dark:text-zinc-900 text-base">
                                                        {{ $evento->accion }}</h3>
                                                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">
                                                        <i class="fa fa-user text-blue-500 text-xs"></i>
                                                        <span class="font-semibold">{{ $evento->usuario }}</span>
                                                        <span class="mx-2 text-zinc-400">•</span>
                                                        <i class="fa fa-shield text-green-500 text-xs"></i>
                                                        <span class="font-medium">{{ ucfirst($evento->rol) }}</span>
                                                    </p>
                                                </div>
                                                <div class="flex flex-col items-end">
                                                    <span
                                                        class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">
                                                        {{ $evento->fecha->format('d/m/Y') }}
                                                    </span>
                                                    <span class="text-xs text-zinc-500 dark:text-zinc-400">
                                                        {{ $evento->fecha->format('H:i') }}
                                                    </span>
                                                </div>
                                            </div>

                                            {{-- Cambios de estado --}}
                                            @if ($evento->estado_anterior || $evento->estado_nuevo)
                                                <div
                                                    class="flex items-center gap-2 mb-3 p-3 bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-600">
                                                    <span
                                                        class="text-sm font-semibold text-zinc-600 dark:text-zinc-400">
                                                        <i class="fa fa-exchange text-xs"></i> Estado:
                                                    </span>
                                                    @if ($evento->estado_anterior)
                                                        <span
                                                            class="px-3 py-1 rounded-full text-xs font-bold bg-zinc-200 dark:bg-zinc-600 text-zinc-800 dark:text-zinc-200">
                                                            {{ ucfirst(str_replace('_', ' ', $evento->estado_anterior)) }}
                                                        </span>
                                                        <i class="fa fa-arrow-right text-zinc-400 text-xs mx-1"></i>
                                                    @endif
                                                    @if ($evento->estado_nuevo)
                                                        @php
                                                            $estadoBadgeClass = match ($evento->estado_nuevo) {
                                                                'pendiente'
                                                                    => 'bg-yellow-100 dark:bg-yellow-900/40 text-yellow-800 dark:text-yellow-200 border border-yellow-300 dark:border-yellow-700',
                                                                'en_proceso'
                                                                    => 'bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-200 border border-blue-300 dark:border-blue-700',
                                                                'completado'
                                                                    => 'bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-200 border border-green-300 dark:border-green-700',
                                                                default
                                                                    => 'bg-zinc-100 dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200',
                                                            };
                                                        @endphp
                                                        <span
                                                            class="px-3 py-1 rounded-full text-xs font-bold {{ $estadoBadgeClass }}">
                                                            {{ ucfirst(str_replace('_', ' ', $evento->estado_nuevo)) }}
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif

                                            {{-- Cambios de técnico --}}
                                            @if ($evento->tecnico_anterior || $evento->tecnico_nuevo)
                                                <div
                                                    class="flex items-center gap-2 mb-3 p-3 bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-600">
                                                    <span
                                                        class="text-sm font-semibold text-zinc-600 dark:text-zinc-400">
                                                        <i class="fa fa-user-md text-xs"></i> Técnico:
                                                    </span>
                                                    @if ($evento->tecnico_anterior)
                                                        <span
                                                            class="px-3 py-1 rounded-full text-xs font-bold bg-zinc-200 dark:bg-zinc-600 text-zinc-800 dark:text-zinc-200">
                                                            {{ $evento->tecnico_anterior }}
                                                        </span>
                                                        <i class="fa fa-arrow-right text-zinc-400 text-xs mx-1"></i>
                                                    @endif
                                                    @if ($evento->tecnico_nuevo)
                                                        <span
                                                            class="px-3 py-1 rounded-full text-xs font-bold bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-200 border border-green-300 dark:border-green-700">
                                                            {{ $evento->tecnico_nuevo }}
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif

                                            {{-- Comentario --}}
                                            @if ($evento->comentario)
                                                <div
                                                    class="mt-3 p-3 bg-white dark:bg-zinc-800 rounded-lg border-l-4 border-purple-500">
                                                    <p class="text-sm text-zinc-700 dark:text-zinc-300 italic">
                                                        <i class="fa fa-quote-left text-purple-500 text-xs mr-1"></i>
                                                        {{ $evento->comentario }}
                                                        <i class="fa fa-quote-right text-purple-500 text-xs ml-1"></i>
                                                    </p>
                                                </div>
                                            @endif

                                            {{-- Foto --}}
                                            @if ($evento->foto)
                                                <p class="text-xs font-semibold text-zinc-600 dark:text-zinc-400 mb-2">
                                                    <i class="fa fa-paperclip"></i> Evidencia adjunta:
                                                </p>
                                                <a href="{{ $evento->foto_url }}" target="_blank"
                                                    class="inline-block group">
                                                    <img src="{{ $evento->foto_url }}" alt="Evidencia"
                                                        class="h-40 w-auto rounded-lg border-2 border-zinc-300 dark:border-zinc-600 group-hover:border-blue-500 dark:group-hover:border-blue-400 group-hover:scale-105 transition-all cursor-pointer shadow-md">
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="bg-zinc-100 dark:bg-zinc-700/50 p-8 rounded-lg text-center">
                            <i class="fa fa-inbox text-5xl text-zinc-400 dark:text-zinc-500 mb-3"></i>
                            <p class="text-zinc-600 dark:text-zinc-400 font-medium">No hay actividades
                                registradas</p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-500 mt-2">Los eventos se registrarán
                                automáticamente</p>
                        </div>
                    @endif
                </div>
            </div>
            {{-- Botones de Acción (Alineados a la derecha) --}}
            <div class="flex justify-end gap-3">
                <a href="{{ route('tickets.edit', $ticket) }}">
                    <button type="button"
                        class="bg-green-500 hover:bg-green-600 text-white font-semibold px-6 py-3 rounded-lg transition flex items-center gap-2 shadow-md">
                        <i class="fa fa-pencil"></i>
                        Editar Ticket
                    </button>
                </a>

                <form action="{{ route('tickets.destroy', $ticket) }}" method="POST"
                    onsubmit="return confirm('¿Está seguro que desea eliminar este ticket? Esta acción no se puede deshacer.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="bg-red-500 hover:bg-red-600 text-white font-semibold px-6 py-3 rounded-lg transition flex items-center gap-2 shadow-md">
                        <i class="fa fa-trash"></i>
                        Eliminar Ticket
                    </button>
                </form>
            </div>
        </div>
    </div>
    {{-- Toggle de tema (si no lo tienes implementado) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Función para alternar entre modo claro y oscuro
            const toggleTheme = () => {
                document.documentElement.classList.toggle('dark');
                localStorage.setItem('theme', document.documentElement.classList.contains('dark') ? 'dark' :
                    'light');
            };

            // Cargar tema guardado
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)')
                    .matches)) {
                document.documentElement.classList.add('dark');
            }

            // Agregar event listener al botón de toggle si existe
            const themeToggle = document.getElementById('theme-toggle');
            if (themeToggle) {
                themeToggle.addEventListener('click', toggleTheme);
            }
        });
    </script>

    {{-- CSS adicional para animaciones suaves --}}
    <style>
        @keyframes pulse-ring {
            0% {
                transform: scale(0.33);
                opacity: 1;
            }

            80%,
            100% {
                transform: scale(2.33);
                opacity: 0;
            }
        }

        .animate-pulse-ring {
            animation: pulse-ring 1.5s infinite;
        }

        /* Transiciones suaves para el cambio de tema */
        * {
            transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
        }
    </style>
</x-layouts.app>
