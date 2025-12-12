<x-layouts.app :title="$title ?? 'Informes'">
    <div class="min-h-screen px-4 py-8 bg-zinc-50 dark:bg-zinc-800 transition-all">
        <div class="max-w-7xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-zinc-800 dark:text-white">Informes</h1>

                <a href="{{ route('informes.create') }}"
                    class="bg-zinc-200 dark:bg-zinc-700 hover:bg-zinc-300
                          dark:hover:bg-zinc-600 text-zinc-800 dark:text-white
                          font-semibold px-6 py-2 rounded-lg transition">
                    <i class="fa fa-plus"></i>
                    Nuevo Informe
                </a>
            </div>

            <div x-data="{ tab: '{{ $filters['tab'] ?? 'correctivos' }}' }" x-cloak>
                {{-- Filtros --}}
                <div class="mb-6 bg-white dark:bg-zinc-900 rounded-lg shadow p-4">
                    <form method="GET" action="{{ route('informes.index') }}" class="flex gap-4 flex-wrap items-end">
                        {{-- Mantener tab activo en el request --}}
                        <input type="hidden" name="tab" x-model="tab">

                        <div class="flex-1 min-w-[200px]">
                            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                                placeholder="Buscar por folio, reporte, equipo, cliente o centro..."
                                class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-700
                                          rounded-lg bg-white dark:bg-zinc-800
                                          text-zinc-900 dark:text-white
                                          focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div class="min-w-[180px]">
                            <select name="cliente_id"
                                class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-700
                                           rounded-lg bg-white dark:bg-zinc-800
                                           text-zinc-900 dark:text-white
                                           focus:ring-2 focus:ring-blue-500">
                                <option value="">Todos los clientes</option>
                                @foreach ($clientes as $cliente)
                                    <option value="{{ $cliente->id }}"
                                        {{ ($filters['cliente_id'] ?? null) == $cliente->id ? 'selected' : '' }}>
                                        {{ $cliente->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="min-w-[220px]">
                            <select name="centro_medico_id"
                                class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-700
                                           rounded-lg bg-white dark:bg-zinc-800
                                           text-zinc-900 dark:text-white
                                           focus:ring-2 focus:ring-blue-500">
                                <option value="">Todos los centros médicos</option>
                                @foreach ($centrosMedicos as $centro)
                                    <option value="{{ $centro->id }}"
                                        {{ ($filters['centro_medico_id'] ?? null) == $centro->id ? 'selected' : '' }}>
                                        {{ $centro->centro_dialisis }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit"
                            class="bg-blue-500 hover:bg-blue-600 text-white font-semibold
                                       px-4 py-2 rounded-lg transition"
                            title="Buscar">
                            <i class="fa fa-search"></i>
                        </button>

                        <button type="button" onclick="window.location='{{ route('informes.index') }}'"
                            class="bg-green-500 hover:bg-green-600 text-white font-semibold
                                       px-4 py-2 rounded-lg transition"
                            title="Limpiar">
                            <i class="fa fa-refresh"></i>
                        </button>
                    </form>
                </div>

                {{-- Tabs --}}
                <div class="mb-4 border-b border-zinc-200 dark:border-zinc-700">
                    <nav class="flex gap-4">
                        <button type="button" @click="tab = 'correctivos'"
                            :class="tab === 'correctivos'
                                ?
                                'border-b-2 border-blue-500 text-blue-600 dark:text-blue-400' :
                                'text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200'"
                            class="px-4 py-2 font-semibold flex items-center gap-2">
                            <span>Correctivos</span>
                            <span
                                class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-semibold
                                       bg-zinc-200 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-200">
                                {{ $correctivos->total() }}
                            </span>
                        </button>

                        <button type="button" @click="tab = 'preventivos'"
                            :class="tab === 'preventivos'
                                ?
                                'border-b-2 border-blue-500 text-blue-600 dark:text-blue-400' :
                                'text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200'"
                            class="px-4 py-2 font-semibold flex items-center gap-2">
                            <span>Preventivos</span>
                            <span
                                class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-semibold
                                       bg-zinc-200 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-200">
                                {{ $preventivos->total() }}
                            </span>
                        </button>
                    </nav>
                </div>

                {{-- Tabla: Correctivos --}}
                <div x-show="tab === 'correctivos'">
                    <div class="overflow-x-auto rounded-lg shadow bg-white dark:bg-zinc-900">
                        <table class="w-full table-auto text-left text-sm">
                            <thead>
                                <tr class="bg-white dark:bg-zinc-700">
                                    @php
                                        $correctivoSorts = [
                                            'numero_folio' => 'Folio',
                                            'fecha_servicio' => 'Fecha Servicio',
                                            'cliente' => 'Cliente',
                                            'centro' => 'Sucursal',
                                            'equipo' => 'Equipo - ID',
                                            'tecnico' => 'Técnico',
                                            'condicion_equipo' => 'Condición',
                                        ];

                                        $buildSortUrl = function ($column, $currentSort, $currentDir) {
                                            $isActive = $currentSort === $column;
                                            $dir = 'asc';

                                            if ($isActive) {
                                                $dir = $currentDir === 'asc' ? 'desc' : null;
                                            }

                                            $query = request()->query();
                                            $query['tab'] = 'correctivos';

                                            if ($dir) {
                                                $query['sort_correctivos'] = $column;
                                                $query['dir_correctivos'] = $dir;
                                            } else {
                                                unset($query['sort_correctivos'], $query['dir_correctivos']);
                                            }

                                            return request()->url() . '?' . http_build_query($query);
                                        };

                                        $renderSortIcon = function ($column, $currentSort, $currentDir) {
                                            if ($currentSort !== $column || !$currentDir) {
                                                return '<i class="fa fa-sort text-xs text-zinc-400"></i>';
                                            }

                                            return $currentDir === 'asc'
                                                ? '<i class="fa fa-sort-up text-xs"></i>'
                                                : '<i class="fa fa-sort-down text-xs"></i>';
                                        };
                                    @endphp

                                    @foreach ($correctivoSorts as $column => $label)
                                        <th class="p-3 text-zinc-700 dark:text-white font-semibold">
                                            <a href="{{ $buildSortUrl($column, $sortCorrectivos, $dirCorrectivos) }}"
                                                class="inline-flex items-center gap-1 hover:text-blue-600 dark:hover:text-blue-300">
                                                <span>{{ $label }}</span>
                                                {!! $renderSortIcon($column, $sortCorrectivos, $dirCorrectivos) !!}
                                            </a>
                                        </th>
                                    @endforeach
                                    <th class="p-3 text-center text-zinc-700 dark:text-white font-semibold">Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($correctivos as $inf)
                                    <tr
                                        class="even:bg-zinc-100 odd:bg-white dark:even:bg-zinc-800 dark:odd:bg-zinc-900
                                               border-b border-zinc-200 dark:border-zinc-700
                                               transition-all hover:bg-zinc-200/40 dark:hover:bg-zinc-700/30">
                                        <td class="p-3 font-medium text-zinc-900 dark:text-zinc-100">
                                            {{ $inf->numero_folio }}
                                        </td>
                                        <td class="p-3 text-zinc-800 dark:text-zinc-300">
                                            {{ optional($inf->fecha_servicio)->format('d/m/Y') }}
                                        </td>
                                        <td class="p-3 text-zinc-800 dark:text-zinc-300">
                                            {{ $inf->cliente->nombre ?? '—' }}
                                        </td>
                                        @php
                                            $sucursalCorrectivo =
                                                optional($inf->centroMedico)->centro_dialisis ?:
                                                optional($inf->centroMedico)->nombre ?:
                                                optional($inf->centroMedico)->nombre_completo ?:
                                                '—';
                                        @endphp
                                        <td class="p-3 text-zinc-800 dark:text-zinc-300">
                                            {{ $sucursalCorrectivo }}
                                        </td>
                                        <td class="p-3 text-zinc-800 dark:text-zinc-300">
                                            @if ($inf->equipo?->modelo)
                                                {{ $inf->equipo->modelo }}
                                            @endif
                                            – ID:
                                            {{ $inf->equipo->codigo ?? '' }}
                                        </td>
                                        <td class="p-3 text-zinc-800 dark:text-zinc-300">
                                            {{ $inf->usuario->name ?? '—' }}
                                        </td>
                                        <td class="p-3">
                                            @php
                                                $cond = $inf->condicion_equipo;
                                                $label = ucfirst(str_replace('_', ' ', $cond));
                                                $condClass = match ($cond) {
                                                    'operativo'
                                                        => 'bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-200',
                                                    'en_observacion'
                                                        => 'bg-yellow-100 dark:bg-yellow-900/40 text-yellow-800 dark:text-yellow-200',
                                                    'fuera_de_servicio'
                                                        => 'bg-red-100 dark:bg-red-900/40 text-red-800 dark:text-red-200',
                                                    default
                                                        => 'bg-zinc-100 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-200',
                                                };
                                            @endphp
                                            <span
                                                class="inline-block rounded-full px-3 py-1 text-xs font-semibold {{ $condClass }}">
                                                {{ $label }}
                                            </span>
                                        </td>
                                        <td class="p-3 text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                <a href="{{ route('informes.correctivo.show', $inf->id) }}"
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg
                                                          bg-blue-500 hover:bg-blue-600 text-white transition-colors duration-200"
                                                    title="Ver">
                                                    <i class="fa fa-eye text-sm"></i>
                                                </a>
                                                <a href="{{ route('informes.download', ['tipo' => 'correctivo', 'id' => $inf->id]) }}"
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg
                                                          bg-red-500 hover:bg-red-600 text-white transition-colors duration-200"
                                                    title="Descargar PDF">
                                                    <i class="fa fa-file-pdf text-sm"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="p-6 text-center text-zinc-600 dark:text-zinc-400">
                                            No se encontraron informes correctivos
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="p-4 bg-white dark:bg-zinc-800">
                            {{ $correctivos->links() }}
                        </div>
                    </div>
                </div>

                {{-- Tabla: Preventivos --}}
                <div x-show="tab === 'preventivos'">
                    <div
                        class="mb-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-lg p-4 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                        <div>
                            <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-50 flex items-center gap-2">
                                <i class="fa fa-clipboard-check text-emerald-500"></i>
                                Generar nuevo informe preventivo
                            </h3>
                            <p class="text-sm text-zinc-600 dark:text-zinc-300">
                                Selecciona el tipo de protocolo para abrir el formulario correspondiente.
                            </p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('informes.preventivos.select-tipo') }}"
                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition">
                                <i class="fa fa-th-list text-xs"></i>
                                Ver tipos disponibles
                            </a>
                            <a href="{{ route('informes.create') }}#preventivos"
                                class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold bg-zinc-200 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-100 rounded-lg hover:bg-zinc-300 dark:hover:bg-zinc-700 transition">
                                <i class="fa fa-arrow-up-right-from-square text-xs"></i>
                                Abrir pestaña Preventivo
                            </a>
                        </div>
                    </div>

                    <div class="overflow-x-auto rounded-lg shadow bg-white dark:bg-zinc-900">
                        <table class="w-full table-auto text-left text-sm">
                            <thead>
                                <tr class="bg-white dark:bg-zinc-700">
                                    @php
                                        $preventivoSorts = [
                                            'numero_reporte_servicio' => 'N° Reporte',
                                            'fecha' => 'Fecha',
                                            'centro' => 'Sucursal',
                                            'equipo' => 'Equipo - ID',
                                            'tecnico' => 'Técnico',
                                            'fecha_proximo_control' => 'Próx. Control',
                                        ];

                                        $buildPreventivoSortUrl = function ($column, $currentSort, $currentDir) {
                                            $isActive = $currentSort === $column;
                                            $dir = 'asc';

                                            if ($isActive) {
                                                $dir = $currentDir === 'asc' ? 'desc' : null;
                                            }

                                            $query = request()->query();
                                            $query['tab'] = 'preventivos';

                                            if ($dir) {
                                                $query['sort_preventivos'] = $column;
                                                $query['dir_preventivos'] = $dir;
                                            } else {
                                                unset($query['sort_preventivos'], $query['dir_preventivos']);
                                            }

                                            return request()->url() . '?' . http_build_query($query);
                                        };
                                    @endphp

                                    @foreach ($preventivoSorts as $column => $label)
                                        <th class="p-3 text-zinc-700 dark:text-white font-semibold">
                                            <a href="{{ $buildPreventivoSortUrl($column, $sortPreventivos, $dirPreventivos) }}"
                                                class="inline-flex items-center gap-1 hover:text-blue-600 dark:hover:text-blue-300">
                                                <span>{{ $label }}</span>
                                                {!! $renderSortIcon($column, $sortPreventivos, $dirPreventivos) !!}
                                            </a>
                                        </th>
                                    @endforeach
                                    <th class="p-3 text-center text-zinc-700 dark:text-white font-semibold">Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($preventivos as $inf)
                                    <tr
                                        class="even:bg-zinc-100 odd:bg-white dark:even:bg-zinc-800 dark:odd:bg-zinc-900
                                               border-b border-zinc-200 dark:border-zinc-700
                                               transition-all hover:bg-zinc-200/40 dark:hover:bg-zinc-700/30">
                                        <td class="p-3 font-medium text-zinc-900 dark:text-zinc-100">
                                            {{ $inf->numero_reporte_servicio }}
                                        </td>
                                        <td class="p-3 text-zinc-800 dark:text-zinc-300">
                                            {{ optional($inf->fecha)->format('d/m/Y') }}
                                        </td>
                                        @php
                                            $sucursalPreventivo =
                                                optional($inf->centroMedico)->centro_dialisis ?:
                                                optional($inf->centroMedico)->nombre ?:
                                                optional($inf->centroMedico)->nombre_completo ?:
                                                '—';
                                        @endphp
                                        <td class="p-3 text-zinc-800 dark:text-zinc-300">
                                            {{ $sucursalPreventivo }}
                                        </td>
                                        <td class="p-3 text-zinc-800 dark:text-zinc-300">
                                            @if ($inf->equipo)
                                                {{ $inf->equipo->modelo }} - ID:
                                                {{ $inf->equipo->id }}
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td class="p-3 text-zinc-800 dark:text-zinc-300">
                                            {{ $inf->usuario->name ?? '—' }}
                                        </td>
                                        <td class="p-3 text-zinc-800 dark:text-zinc-300">
                                            {{ optional($inf->fecha_proximo_control)->format('d/m/Y') ?? 'No especificada' }}
                                        </td>
                                        <td class="p-3 text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                <a href="{{ route('informes.preventivo.show', $inf->id) }}"
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg
                                                          bg-blue-500 hover:bg-blue-600 text-white transition-colors duration-200"
                                                    title="Ver">
                                                    <i class="fa fa-eye text-sm"></i>
                                                </a>
                                                <a href="{{ route('informes.download', ['tipo' => 'preventivo', 'id' => $inf->id]) }}"
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg
                                                          bg-red-500 hover:bg-red-600 text-white transition-colors duration-200"
                                                    title="Descargar PDF">
                                                    <i class="fa fa-file-pdf text-sm"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="p-6 text-center text-zinc-600 dark:text-zinc-400">
                                            No se encontraron informes preventivos
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="p-4 bg-white dark:bg-zinc-800">
                            {{ $preventivos->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
