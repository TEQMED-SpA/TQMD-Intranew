<x-layouts.app :title="'Equipo: ' . ($equipo->nombre ?? 'Detalle')">
    <div class="max-w-5xl mx-auto px-4 py-8">
        {{-- Header + acciones --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-zinc-800 dark:text-white">
                    {{ $equipo->nombre ?? 'Equipo' }}
                </h1>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                    Código: <span
                        class="font-medium text-zinc-700 dark:text-zinc-200">{{ $equipo->codigo ?? '—' }}</span>
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('equipos.edit', $equipo) }}"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-green-600 hover:bg-green-700 text-white transition">
                    <i class="fa fa-pencil"></i> Editar
                </a>
                <form action="{{ route('equipos.destroy', $equipo) }}" method="POST"
                    onsubmit="return confirm('¿dar de baja el equipo?')" class="inline">
                    @csrf @method('DELETE')
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white transition">
                        <i class="fa fa-trash"></i> Dar de baja
                    </button>
                </form>
                <a href="{{ route('equipos.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-zinc-200 dark:bg-zinc-700 hover:bg-zinc-300 dark:hover:bg-zinc-600 text-zinc-800 dark:text-white transition">
                    <i class="fa fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>

        {{-- Resumen de estado / mantención --}}
        @php
            $fecha = $equipo->proxima_mantencion;
            $dias = $fecha ? now()->diffInDays($fecha, false) : null;
            if (is_null($fecha)) {
                $mantLabel = 'N/A';
                $mantClass = 'bg-zinc-200 dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200';
            } elseif ($dias < 0) {
                $mantLabel = 'Vencida';
                $mantClass = 'bg-red-600 text-white';
            } elseif ($dias <= 30) {
                $mantLabel = 'Próxima';
                $mantClass = 'bg-yellow-500 text-black';
            } else {
                $mantLabel = 'Al día';
                $mantClass = 'bg-green-600 text-white';
            }

            $estadoColor = match ($equipo->estado) {
                'Operativo' => 'bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-200',
                'En observacion' => 'bg-yellow-100 dark:bg-yellow-900/40 text-yellow-800 dark:text-yellow-200',
                'Fuera de servicio' => 'bg-red-100 dark:bg-red-900/40 text-red-800 dark:text-red-200',
                'Baja' => 'bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-200',
                default => 'bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-200',
            };
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="rounded-lg shadow bg-white dark:bg-zinc-900 p-4">
                <div class="text-sm text-zinc-500 dark:text-zinc-400 mb-1">Estado</div>
                <span class="inline-block rounded-full px-3 py-1 text-xs font-semibold {{ $estadoColor }}">
                    {{ $equipo->estado ?? 'Sin estado' }}
                </span>
            </div>
            <div class="rounded-lg shadow bg-white dark:bg-zinc-900 p-4">
                <div class="text-sm text-zinc-500 dark:text-zinc-400 mb-1">Tipo mantención</div>
                <div
                    class="px-2 py-1 rounded bg-yellow-100 dark:bg-yellow-900/40 text-yellow-800 dark:text-yellow-200 inline-flex items-center gap-2">
                    {{ $equipo->tipo_mantencion ?? '—' }}
                    @if (($equipo->cant_dias_fuera_serv ?? 0) > 0)
                        <span class="text-xs font-semibold text-zinc-600 dark:text-zinc-300">(Fuera
                            {{ $equipo->cant_dias_fuera_serv }} días)</span>
                    @endif
                </div>
            </div>
            <div class="rounded-lg shadow bg-white dark:bg-zinc-900 p-4">
                <div class="text-sm text-zinc-500 dark:text-zinc-400 mb-1">Próxima mantención</div>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-1 rounded {{ $mantClass }}">
                        {{ $fecha ? \Carbon\Carbon::parse($fecha)->format('d-m-Y') : '—' }}
                    </span>
                    <small class="text-xs text-zinc-500 dark:text-zinc-400">{{ $mantLabel }}</small>
                </div>
            </div>
        </div>

        {{-- TARJETA CONTENEDORA GRANDE --}}
        <div class="rounded-xl shadow-lg bg-white dark:bg-zinc-900 p-4 md:p-6">
            <div class="text-zinc-800 dark:text-white font-semibold mb-4">
                Información del equipo
            </div>

            {{-- Tarjetas internas apiladas --}}
            <div class="space-y-6">

                {{-- Tarjeta: Detalles del equipo --}}
                <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                    <div
                        class="px-4 py-3 border-b border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800
                                text-zinc-800 dark:text-white font-semibold">
                        Detalles del equipo
                    </div>
                    <table class="w-full table-auto text-left text-sm border-collapse">
                        <tbody>
                            <tr class="border-b border-zinc-200 dark:border-zinc-700">
                                <th
                                    class="w-56 p-3 text-zinc-600 dark:text-zinc-300 font-medium border-r border-zinc-200 dark:border-zinc-700">
                                    Código</th>
                                <td class="p-3 text-zinc-900 dark:text-zinc-100">{{ $equipo->codigo ?? '—' }}</td>
                            </tr>
                            <tr class="border-b border-zinc-200 dark:border-zinc-700">
                                <th
                                    class="w-56 p-3 text-zinc-600 dark:text-zinc-300 font-medium border-r border-zinc-200 dark:border-zinc-700">
                                    Nombre</th>
                                <td class="p-3 text-zinc-900 dark:text-zinc-100">{{ $equipo->nombre ?? '—' }}</td>
                            </tr>
                            <tr class="border-b border-zinc-200 dark:border-zinc-700">
                                <th
                                    class="w-56 p-3 text-zinc-600 dark:text-zinc-300 font-medium border-r border-zinc-200 dark:border-zinc-700">
                                    Modelo</th>
                                <td class="p-3 text-zinc-900 dark:text-zinc-100">{{ $equipo->modelo ?? '—' }}</td>
                            </tr>
                            <tr class="border-b border-zinc-200 dark:border-zinc-700">
                                <th
                                    class="w-56 p-3 text-zinc-600 dark:text-zinc-300 font-medium border-r border-zinc-200 dark:border-zinc-700">
                                    Marca</th>
                                <td class="p-3 text-zinc-900 dark:text-zinc-100">{{ $equipo->marca ?? '—' }}</td>
                            </tr>
                            <tr class="border-b border-zinc-200 dark:border-zinc-700">
                                <th
                                    class="w-56 p-3 text-zinc-600 dark:text-zinc-300 font-medium border-r border-zinc-200 dark:border-zinc-700">
                                    Tipo de equipo</th>
                                <td class="p-3 text-zinc-900 dark:text-zinc-100">
                                    {{ $equipo->tipo?->nombre ?? '—' }}
                                </td>
                            </tr>
                            <tr class="border-b border-zinc-200 dark:border-zinc-700">
                                <th
                                    class="w-56 p-3 text-zinc-600 dark:text-zinc-300 font-medium border-r border-zinc-200 dark:border-zinc-700">
                                    ID máquina</th>
                                <td class="p-3 text-zinc-900 dark:text-zinc-100">{{ $equipo->id_maquina ?? '—' }}</td>
                            </tr>
                            <tr class="border-b border-zinc-200 dark:border-zinc-700">
                                <th
                                    class="w-56 p-3 text-zinc-600 dark:text-zinc-300 font-medium border-r border-zinc-200 dark:border-zinc-700">
                                    Nº serie</th>
                                <td class="p-3 text-zinc-900 dark:text-zinc-100">{{ $equipo->numero_serie ?? '—' }}
                                </td>
                            </tr>
                            <tr class="border-b border-zinc-200 dark:border-zinc-700">
                                <th
                                    class="w-56 p-3 text-zinc-600 dark:text-zinc-300 font-medium border-r border-zinc-200 dark:border-zinc-700">
                                    Horas de uso</th>
                                <td class="p-3 text-zinc-900 dark:text-zinc-100">
                                    {{ is_null($equipo->horas_uso) ? '—' : number_format($equipo->horas_uso, 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr class="border-b border-zinc-200 dark:border-zinc-700">
                                <th
                                    class="w-56 p-3 text-zinc-600 dark:text-zinc-300 font-medium border-r border-zinc-200 dark:border-zinc-700">
                                    Días fuera de servicio
                                </th>
                                <td class="p-3 text-zinc-900 dark:text-zinc-100">
                                    {{ ($equipo->cant_dias_fuera_serv ?? 0) > 0 ? $equipo->cant_dias_fuera_serv . ' día(s)' : 'Sin registro' }}
                                </td>
                            </tr>
                            <tr class="border-b border-zinc-200 dark:border-zinc-700">
                                <th
                                    class="w-56 p-3 text-zinc-600 dark:text-zinc-300 font-medium border-r border-zinc-200 dark:border-zinc-700">
                                    Últ. mantención</th>
                                <td class="p-3 text-zinc-900 dark:text-zinc-100">
                                    {{ $equipo->ultima_mantencion ? \Carbon\Carbon::parse($equipo->ultima_mantencion)->format('d-m-Y') : '—' }}
                                </td>
                            </tr>
                            <tr>
                                <th
                                    class="w-56 p-3 text-zinc-600 dark:text-zinc-300 font-medium border-r border-zinc-200 dark:border-zinc-700">
                                    Descripción</th>
                                <td class="p-3 text-zinc-900 dark:text-zinc-100 whitespace-pre-line">
                                    {{ $equipo->descripcion ?: '—' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Tarjeta: Ubicación --}}
                <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                    <div
                        class="px-4 py-3 border-b border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800
                                text-zinc-800 dark:text-white font-semibold">
                        Ubicación
                    </div>
                    <table class="w-full table-auto text-left text-sm border-collapse">
                        <tbody>
                            <tr class="border-b border-zinc-200 dark:border-zinc-700">
                                <th
                                    class="w-56 p-3 text-zinc-600 dark:text-zinc-300 font-medium border-r border-zinc-200 dark:border-zinc-700">
                                    Centro médico
                                </th>
                                <td class="p-3 text-zinc-900 dark:text-zinc-100">
                                    {{ $equipo->centro?->centro_dialisis ?? '—' }}
                                </td>
                            </tr>
                            <tr>
                                <th
                                    class="w-56 p-3 text-zinc-600 dark:text-zinc-300 font-medium border-r border-zinc-200 dark:border-zinc-700">
                                    Cliente
                                </th>
                                <td class="p-3 text-zinc-900 dark:text-zinc-100">
                                    {{ $equipo->centro?->cliente?->nombre ?? '—' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div> {{-- /space-y-6 --}}
        </div> {{-- /tarjeta contenedora --}}

        {{-- Metadatos pequeños --}}
        <div class="mt-4 text-xs text-zinc-500 dark:text-zinc-400">
            Creado: {{ optional($equipo->created_at)->format('d-m-Y H:i') ?? '—' }} ·
            Actualizado: {{ optional($equipo->updated_at)->format('d-m-Y H:i') ?? '—' }}
        </div>
    </div>
</x-layouts.app>
