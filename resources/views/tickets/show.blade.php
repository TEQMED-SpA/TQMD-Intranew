<x-layouts.app :title="$title ?? 'Detalle Ticket'">
    <div class="min-h-screen px-4 py-8 bg-zinc-50 dark:bg-zinc-800 transition-all">
        <div class="max-w-2xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-zinc-800 dark:text-white">Detalle del Ticket</h1>
                <a href="{{ route('tickets.index') }}">
                    <button type="button"
                        class="bg-zinc-200 dark:bg-zinc-700 text-zinc-800 dark:text-zinc-100 font-semibold px-4 py-2 rounded-lg hover:bg-zinc-300 dark:hover:bg-zinc-600 transition flex items-center gap-2">
                        <i class="fa fa-arrow-left"></i>
                        Volver
                    </button>
                </a>
            </div>
            <div class="rounded-lg shadow bg-white dark:bg-zinc-900 p-6">
                <ul class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    <li class="py-3 flex justify-between items-center">
                        <span class="font-semibold text-zinc-700 dark:text-zinc-200">Número de Ticket:</span>
                        <span class="text-zinc-900 dark:text-zinc-100">{{ $ticket->numero_ticket }}</span>
                    </li>
                    <li class="py-3 flex justify-between items-center">
                        <span class="font-semibold text-zinc-700 dark:text-zinc-200">Estado:</span>
                        @php
                            $estadoConfig = [
                                'pendiente' => [
                                    'class' =>
                                        'bg-yellow-100 dark:bg-yellow-900/40 text-yellow-800 dark:text-yellow-200',
                                    'text' => 'Pendiente',
                                ],
                                'en_proceso' => [
                                    'class' => 'bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-200',
                                    'text' => 'En Proceso',
                                ],
                                'completado' => [
                                    'class' => 'bg-green-200 dark:bg-green-800 text-green-900 dark:text-zinc-800',
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
                    </li>
                    <li class="py-3 flex justify-between items-center">
                        <span class="font-semibold text-zinc-700 dark:text-zinc-200">Cliente:</span>
                        <span class="text-zinc-900 dark:text-zinc-100">{{ $ticket->cliente }}</span>
                    </li>
                    <li class="py-3 flex justify-between items-center">
                        <span class="font-semibold text-zinc-700 dark:text-zinc-200">Solicitante:</span>
                        <span class="text-zinc-900 dark:text-zinc-100">{{ $ticket->nombre_apellido }}</span>
                    </li>
                    <li class="py-3 flex justify-between items-center">
                        <span class="font-semibold text-zinc-700 dark:text-zinc-200">Cargo:</span>
                        <span class="text-zinc-900 dark:text-zinc-100">{{ $ticket->cargo }}</span>
                    </li>
                    <li class="py-3 flex justify-between items-center">
                        <span class="font-semibold text-zinc-700 dark:text-zinc-200">Teléfono:</span>
                        <span class="text-zinc-900 dark:text-zinc-100">{{ $ticket->telefono }}</span>
                    </li>
                    <li class="py-3 flex justify-between items-center">
                        <span class="font-semibold text-zinc-700 dark:text-zinc-200">Email:</span>
                        <span class="text-zinc-900 dark:text-zinc-100">{{ $ticket->email ?: 'No registrado' }}</span>
                    </li>
                    <li class="py-3 flex justify-between items-center">
                        <span class="font-semibold text-zinc-700 dark:text-zinc-200">Técnico Asignado:</span>
                        <span
                            class="text-zinc-900 dark:text-zinc-100">{{ $ticket->tecnicoAsignado->name ?? 'Sin asignar' }}</span>
                    </li>
                    <li class="py-3 flex justify-between items-center">
                        <span class="font-semibold text-zinc-700 dark:text-zinc-200">Fecha de Visita:</span>
                        <span
                            class="text-zinc-900 dark:text-zinc-100">{{ $ticket->fecha_visita ? $ticket->fecha_visita->format('d/m/Y H:i') : 'No programada' }}</span>
                    </li>
                    <li class="py-3 flex justify-between items-center">
                        <span class="font-semibold text-zinc-700 dark:text-zinc-200">ID/Nº Equipo:</span>
                        <span
                            class="text-zinc-900 dark:text-zinc-100">{{ $ticket->id_numero_equipo ?: 'No especificado' }}</span>
                    </li>
                    <li class="py-3 flex justify-between items-center">
                        <span class="font-semibold text-zinc-700 dark:text-zinc-200">Modelo de Máquina:</span>
                        <span
                            class="text-zinc-900 dark:text-zinc-100">{{ $ticket->modelo_maquina ?: 'No especificado' }}</span>
                    </li>
                    <li class="py-3 flex justify-between items-center">
                        <span class="font-semibold text-zinc-700 dark:text-zinc-200">Momento de la Falla:</span>
                        <span class="text-zinc-900 dark:text-zinc-100">{{ $ticket->momento_falla }}</span>
                    </li>
                    @if ($ticket->momento_falla === 'Otras' && $ticket->momento_falla_otras)
                        <li class="py-3 flex justify-between items-center">
                            <span class="font-semibold text-zinc-700 dark:text-zinc-200">Especificar Momento:</span>
                            <span class="text-zinc-900 dark:text-zinc-100">{{ $ticket->momento_falla_otras }}</span>
                        </li>
                    @endif
                    <li class="py-3 flex flex-col gap-2">
                        <span class="font-semibold text-zinc-700 dark:text-zinc-200">Falla Presentada:</span>
                        <span
                            class="text-zinc-900 dark:text-zinc-100 bg-zinc-50 dark:bg-zinc-800 p-3 rounded-lg">{{ $ticket->falla_presentada }}</span>
                    </li>
                    <li class="py-3 flex flex-col gap-2">
                        <span class="font-semibold text-zinc-700 dark:text-zinc-200">Acciones Realizadas:</span>
                        <span
                            class="text-zinc-900 dark:text-zinc-100 bg-zinc-50 dark:bg-zinc-800 p-3 rounded-lg">{{ $ticket->acciones_realizadas ?: 'No se han registrado acciones' }}</span>
                    </li>
                    <li class="py-3 flex justify-between items-center">
                        <span class="font-semibold text-zinc-700 dark:text-zinc-200">Creado:</span>
                        <span
                            class="text-zinc-700 dark:text-zinc-300">{{ $ticket->created_at->format('d-m-Y H:i') }}</span>
                    </li>
                </ul>
                <div class="flex justify-end mt-6 gap-2">
                    <a href="{{ route('tickets.edit', $ticket) }}">
                        <button type="button"
                            class="bg-green-500 hover:bg-green-600 text-white font-semibold px-4 py-2 rounded-lg transition flex items-center gap-2">
                            <i class="fa fa-pencil"></i>
                            Editar
                        </button>
                    </a>
                    <form action="{{ route('tickets.destroy', $ticket) }}" method="POST"
                        onsubmit="return confirm('¿Eliminar ticket?');">
                        @csrf @method('DELETE')
                        <button type="submit"
                            class="bg-red-500 hover:bg-red-600 text-white font-semibold px-4 py-2 rounded-lg transition flex items-center gap-2">
                            <i class="fa fa-trash"></i>
                            Eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
