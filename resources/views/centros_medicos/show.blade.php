@php
    $nombreCentro = $centroMedico->nombre ?? ($centroMedico->centro_dialisis ?? 'Centro Médico');
    $codigoCentro = $centroMedico->cod_centro_medico;
@endphp

<x-layouts.app :title="'Centro: ' . $nombreCentro">
    <div class="min-h-screen px-4 py-8 bg-zinc-50 dark:bg-zinc-900 transition-all">
        <div class="max-w-6xl mx-auto space-y-6">
            {{-- Header --}}
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <p class="text-sm uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Centros médicos</p>
                    <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">
                        {{ $nombreCentro }}
                    </h1>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">
                        {{ $centroMedico->direccion ? $centroMedico->direccion . ' · ' : '' }}
                        {{ $centroMedico->region ?? 'Sin región' }}
                    </p>
                </div>
                <div class="flex flex-wrap gap-2">
                    @role('admin|auditor')
                        <a href="{{ route('centros_medicos.edit', ['centros_medico' => $centroMedico->id]) }}"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-green-600 hover:bg-green-700 text-white text-sm font-semibold transition">
                            <i class="fa fa-pen"></i>
                            Editar
                        </a>
                    @endrole
                    <a href="{{ route('centros_medicos.index') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-zinc-300 dark:border-zinc-700 text-sm font-semibold text-zinc-700 dark:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition">
                        <i class="fa fa-arrow-left text-xs"></i>
                        Volver
                    </a>
                </div>
            </div>

            {{-- Resumen --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-4">
                    <p class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Cliente</p>
                    <p class="mt-2 text-lg font-semibold text-zinc-900 dark:text-white">
                        {{ $centroMedico->cliente->nombre ?? 'Sin cliente' }}
                    </p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">
                        Código: {{ $centroMedico->cod_cliente ?? '—' }}
                    </p>
                </div>
                <div class="rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-4">
                    <p class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Equipos</p>
                    <p class="mt-2 text-3xl font-bold text-zinc-900 dark:text-white">
                        {{ number_format($equiposTotal ?? 0) }}
                    </p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Registrados en este centro</p>
                </div>
                <div class="rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-4">
                    <p class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Solicitudes</p>
                    <p class="mt-2 text-lg font-semibold text-zinc-900 dark:text-white">
                        {{ number_format($solicitudesTotal ?? 0) }}
                        <span class="text-sm text-zinc-500 dark:text-zinc-400">
                            ({{ number_format($solicitudesPendientes ?? 0) }} pendientes)
                        </span>
                    </p>
                </div>
                <div class="rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-4">
                    <p class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Estado</p>
                    <div class="mt-3">
                        @if ($centroMedico->activo)
                            <span
                                class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-200">
                                <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                Activo
                            </span>
                        @else
                            <span
                                class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold bg-red-100 dark:bg-red-900/40 text-red-800 dark:text-red-200">
                                <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                Inactivo
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Detalle del centro --}}
            <div
                class="rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-sm p-6">
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4">Información del centro</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-zinc-500 dark:text-zinc-400">Nombre</p>
                        <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $centroMedico->nombre ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-zinc-500 dark:text-zinc-400">Teléfono</p>
                        <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $centroMedico->telefono ?? '—' }}
                        </p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-zinc-500 dark:text-zinc-400">Dirección</p>
                        <p class="font-medium text-zinc-900 dark:text-zinc-100">
                            {{ $centroMedico->direccion ?? '—' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-zinc-500 dark:text-zinc-400">Ciudad</p>
                        <p class="font-medium text-zinc-900 dark:text-zinc-100">
                            {{ $centroMedico->ciudad ?? '—' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-zinc-500 dark:text-zinc-400">Región</p>
                        <p class="font-medium text-zinc-900 dark:text-zinc-100">
                            {{ $centroMedico->region ?? '—' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Módulo de equipos asociados --}}
            <div class="rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-sm">
                <div class="p-6 border-b border-zinc-200 dark:border-zinc-800 space-y-4">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                        <div>
                            <h2 class="text-xl font-semibold text-zinc-900 dark:text-white">
                                Equipos asociados
                            </h2>
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                                Filtra por estado, tipo de equipo o busca por nombre/código.
                            </p>
                        </div>
                        <a href="{{ route('equipos.create') }}"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold transition">
                            <i class="fa fa-plus text-xs"></i>
                            Nuevo equipo
                        </a>
                    </div>

                    {{-- Filtros --}}
                    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <input type="hidden" name="page" value="1">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-zinc-600 dark:text-zinc-300 mb-1">
                                Buscar equipo
                            </label>
                            <input type="text" name="buscar" value="{{ request('buscar') }}"
                                placeholder="Nombre, modelo, serie, código..."
                                class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-sm text-zinc-900 dark:text-zinc-100 px-3 py-2 focus:ring focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-zinc-600 dark:text-zinc-300 mb-1">
                                Estado
                            </label>
                            <select name="estado"
                                class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-sm text-zinc-900 dark:text-zinc-100 focus:ring focus:border-blue-500">
                                <option value="">Todos</option>
                                @foreach ($estadoOpciones as $estado)
                                    <option value="{{ $estado }}" @selected(request('estado') === $estado)>
                                        {{ $estado }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-zinc-600 dark:text-zinc-300 mb-1">
                                Tipo de equipo
                            </label>
                            <select name="tipo_equipo_id"
                                class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-sm text-zinc-900 dark:text-zinc-100 focus:ring focus:border-blue-500">
                                <option value="">Todos</option>
                                @foreach ($tipos_equipo as $tipo)
                                    <option value="{{ $tipo->id }}" @selected(request('tipo_equipo_id') == $tipo->id)>
                                        {{ $tipo->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-4 flex flex-col sm:flex-row gap-3">
                            <button type="submit"
                                class="inline-flex justify-center items-center gap-2 px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold flex-1 sm:flex-initial">
                                <i class="fa fa-magnifying-glass text-xs"></i>
                                Aplicar filtros
                            </button>
                            <a href="{{ url()->current() }}"
                                class="inline-flex justify-center items-center gap-2 px-4 py-2 rounded-lg border border-zinc-300 dark:border-zinc-700 text-sm font-semibold text-zinc-700 dark:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800">
                                Limpiar
                            </a>
                        </div>
                    </form>
                </div>

                {{-- Tabla --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800 text-sm">
                        <thead class="bg-zinc-50 dark:bg-zinc-800/70 text-zinc-600 dark:text-zinc-300">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold">Equipo</th>
                                <th class="px-4 py-3 text-left font-semibold">Modelo / Marca</th>
                                <th class="px-4 py-3 text-left font-semibold">Serie / Código</th>
                                <th class="px-4 py-3 text-left font-semibold">Tipo</th>
                                <th class="px-4 py-3 text-left font-semibold">Estado</th>
                                <th class="px-4 py-3 text-left font-semibold">Mantención</th>
                                <th class="px-4 py-3 text-center font-semibold">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 text-zinc-800 dark:text-zinc-100">
                            @forelse ($equipos->items() as $equipo)
                                @php
                                    $mant = $equipo->proxima_mantencion
                                        ? \Carbon\Carbon::parse($equipo->proxima_mantencion)
                                        : null;
                                    $mantLabel = $mant
                                        ? ($mant->isPast()
                                            ? 'Vencida'
                                            : ($mant->diffInDays(now()) <= 30
                                                ? 'Próxima'
                                                : 'Al día'))
                                        : 'Sin fecha';
                                    $mantClass = match ($mantLabel) {
                                        'Vencida' => 'text-red-600 dark:text-red-300',
                                        'Próxima' => 'text-yellow-600 dark:text-yellow-300',
                                        'Al día' => 'text-green-600 dark:text-green-300',
                                        default => 'text-zinc-500 dark:text-zinc-400',
                                    };
                                @endphp
                                <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition">
                                    <td class="px-4 py-3">
                                        <div class="font-semibold">{{ $equipo->nombre ?? '—' }}</div>
                                        <div class="text-xs text-zinc-500">ID: {{ $equipo->codigo ?? '—' }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div>{{ $equipo->modelo ?? '—' }}</div>
                                        <div class="text-xs text-zinc-500">{{ $equipo->marca ?? '—' }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div>{{ $equipo->numero_serie ?? '—' }}</div>
                                        <div class="text-xs text-zinc-500">Máquina: {{ $equipo->id_maquina ?? '—' }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ $equipo->tipo?->nombre ?? '—' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <span
                                            class="inline-flex px-2 py-1 rounded-full text-xs font-semibold
                                            @class([
                                                'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200' =>
                                                    $equipo->estado === 'Operativo',
                                                'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200' =>
                                                    $equipo->estado === 'En observacion',
                                                'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200' =>
                                                    $equipo->estado === 'Fuera de servicio',
                                                'bg-zinc-200 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-200' => !in_array(
                                                    $equipo->estado,
                                                    ['Operativo', 'En observacion', 'Fuera de servicio']),
                                            ])">
                                            {{ $equipo->estado ?? 'Sin estado' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-semibold {{ $mantClass }}">
                                            {{ $mant ? $mant->format('d/m/Y') : '—' }}
                                        </div>
                                        <div class="text-xs text-zinc-500">{{ $mantLabel }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <a href="{{ route('equipos.show', $equipo->id) }}"
                                            class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-blue-600 hover:bg-blue-700 text-white transition"
                                            title="Ver equipo">
                                            <i class="fa fa-eye text-sm"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-10 text-center text-zinc-500">
                                        No se encontraron equipos con los filtros aplicados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-4 py-3 border-t border-zinc-200 dark:border-zinc-800">
                    {{ $equipos->links() }}
                </div>
            </div>

            <div class="text-xs text-zinc-500 dark:text-zinc-400 text-center">
                Última actualización: {{ optional($centroMedico->updated_at)->format('d-m-Y H:i') ?? '—' }}
            </div>
        </div>
    </div>
</x-layouts.app>
