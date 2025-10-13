<x-layouts.app :title="$title ?? 'Tickets'">
    <div class="min-h-screen px-4 py-8 bg-zinc-50 dark:bg-zinc-800 transition-all">
        <div class="max-w-7xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-zinc-800 dark:text-white">Tickets de Soporte</h1>
            </div>

            <!-- Filtros -->
            <div class="mb-6 bg-white dark:bg-zinc-900 rounded-lg shadow p-4">
                <form method="GET" action="{{ route('tickets.index') }}" class="flex gap-4 flex-wrap">
                    <div class="flex-1 min-w-[200px]">
                        <input type="text" name="buscar" value="{{ request('buscar') }}"
                            placeholder="Buscar por número, cliente, nombre..."
                            class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="min-w-[150px]">
                        <select name="estado"
                            class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                            <option value="">Todos los estados</option>
                            <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente
                            </option>
                            <option value="en_proceso" {{ request('estado') == 'en_proceso' ? 'selected' : '' }}>En
                                Proceso</option>
                            <option value="completado" {{ request('estado') == 'completado' ? 'selected' : '' }}>
                                Completado</option>
                        </select>
                    </div>
                    <button type="submit"
                        class="bg-zinc-100 dark:bg-zinc-600 hover:bg-zinc-400 dark:hover:bg-zinc-800 text-zinc-800 dark:text-white font-semibold px-6 py-2 rounded-lg transition">
                        <i class="fa fa-search"></i> Buscar
                    </button>
                    <a href="{{ route('tickets.index') }}"
                        class="bg-zinc-200 dark:bg-zinc-700 hover:bg-zinc-300 dark:hover:bg-zinc-600 text-zinc-800 dark:text-white font-semibold px-6 py-2 rounded-lg transition">
                        <i class="fa fa-refresh"></i> Limpiar
                    </a>
                </form>
            </div>

            <div class="overflow-x-auto rounded-lg shadow bg-white dark:bg-zinc-900">
                @php
                    $tableRowClass = 'border-transparent hover:border-zinc-200 dark:hover:border-transparent 
                      even:bg-zinc-100 odd:bg-white dark:even:bg-zinc-800 dark:odd:bg-zinc-900 
                      hover:bg-zinc-800/5 dark:hover:bg-white/[7%] 
                      text-zinc-600 dark:text-white/80 
                      hover:text-zinc-800 dark:hover:text-white 
                      transition-all duration-200';
                @endphp

                <table class="w-full table-auto text-left text-sm">
                    <thead>
                        <tr class="bg-white dark:bg-zinc-700">
                            <th class="p-3 text-zinc-700 dark:text-white font-semibold">Nº Ticket</th>
                            <th class="p-3 text-zinc-700 dark:text-white font-semibold">Cliente</th>
                            <th class="p-3 text-zinc-700 dark:text-white font-semibold">Solicitante</th>
                            <th class="p-3 text-zinc-700 dark:text-white font-semibold">Falla</th>
                            <th class="p-3 text-zinc-700 dark:text-white font-semibold">Estado</th>
                            <th class="p-3 text-zinc-700 dark:text-white font-semibold">Técnico</th>
                            <th class="p-3 text-zinc-700 dark:text-white font-semibold">Fecha Visita</th>
                            <th class="p-3 text-center text-zinc-700 dark:text-white font-semibold">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets as $ticket)
                            <tr class="{{ $tableRowClass }}">
                                <td class="p-3 text-zinc-900 dark:text-zinc-100 font-medium">
                                    {{ $ticket->numero_ticket }}
                                </td>
                                <td class="p-3 text-zinc-900 dark:text-zinc-300">
                                    {{ $ticket->cliente }}
                                </td>
                                <td class="p-3 text-zinc-900 dark:text-zinc-300">
                                    {{ $ticket->nombre_apellido }}
                                </td>
                                <td class="p-3 text-zinc-900 dark:text-zinc-300">
                                    {{ Str::limit($ticket->falla_presentada, 40) }}
                                </td>
                                <td class="p-3">
                                    @php
                                        $estadoConfig = [
                                            'pendiente' => [
                                                'class' =>
                                                    'bg-yellow-100 dark:bg-yellow-900/40 text-yellow-800 dark:text-yellow-200',
                                                'text' => 'Pendiente',
                                            ],
                                            'en_proceso' => [
                                                'class' =>
                                                    'bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-200',
                                                'text' => 'En Proceso',
                                            ],
                                            'completado' => [
                                                'class' =>
                                                    'bg-green-200 dark:bg-green-800 text-green-900 dark:text-zinc-800',
                                                'text' => 'Completado',
                                            ],
                                        ];
                                        $config = $estadoConfig[$ticket->estado] ?? [
                                            'class' => 'bg-zinc-100',
                                            'text' => $ticket->estado,
                                        ];
                                    @endphp
                                    <span
                                        class="inline-block rounded-full px-3 py-1 text-xs font-semibold transition-colors duration-200 {{ $config['class'] }}">
                                        {{ $config['text'] }}
                                    </span>
                                </td>
                                <td class="p-3 text-zinc-900 dark:text-zinc-300">
                                    {{ $ticket->tecnicoAsignado->name ?? 'Sin asignar' }}
                                </td>
                                <td class="p-3 text-zinc-900 dark:text-zinc-300">
                                    {{ $ticket->fecha_visita ? $ticket->fecha_visita->format('d/m/Y H:i') : '-' }}
                                </td>
                                <td class="p-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('tickets.show', $ticket) }}"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-500 hover:bg-blue-600 text-white transition-colors duration-200"
                                            title="Ver">
                                            <i class="fa fa-eye text-sm"></i>
                                        </a>
                                        <a href="{{ route('tickets.edit', $ticket) }}"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-green-500 hover:bg-green-600 text-white transition-colors duration-200"
                                            title="Editar">
                                            <i class="fa fa-pencil text-sm"></i>
                                        </a>
                                        <form action="{{ route('tickets.destroy', $ticket) }}" method="POST"
                                            style="display:inline;" onsubmit="return confirm('¿Eliminar ticket?');">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-500 hover:bg-red-600 text-white transition-colors duration-200"
                                                title="Eliminar">
                                                <i class="fa fa-trash text-sm"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="p-6 text-center text-zinc-500 dark:text-zinc-400">
                                    No se encontraron tickets
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-4 bg-white dark:bg-zinc-800">
                    {{ $tickets->links() }}
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
