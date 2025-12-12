<x-layouts.app :title="'Inventario de Técnicos'">
    <div class="min-h-screen px-4 py-8 bg-zinc-50 dark:bg-zinc-900 transition-all">
        <div class="max-w-6xl mx-auto space-y-6">
            {{-- Header --}}
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                        <i class="fa fa-toolbox text-blue-500"></i>
                        Inventario de Técnicos
                    </h1>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">
                        Control y movimientos de repuestos asignados a cada técnico.
                    </p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('repuestos.index') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-zinc-200 dark:border-zinc-700 text-sm font-semibold text-zinc-700 dark:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition">
                        <i class="fa fa-boxes-stacked text-xs"></i>
                        Ver repuestos
                    </a>
                </div>
            </div>

            {{-- Alerts --}}
            @if (session('success'))
                <div
                    class="rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-800 dark:border-emerald-800 dark:bg-emerald-950/30 dark:text-emerald-200 px-4 py-3 text-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div
                    class="rounded-lg border border-red-200 bg-red-50 text-red-700 dark:border-red-800 dark:bg-red-950/30 dark:text-red-200 px-4 py-3 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Filtros --}}
            <div
                class="rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-sm p-6 space-y-4">
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-white flex items-center gap-2">
                    <i class="fa fa-filter text-blue-500"></i>
                    Filtros
                </h2>

                <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    @if ($isAdminOrAuditor)
                        <div>
                            <label class="block text-sm font-semibold text-zinc-600 dark:text-zinc-300 mb-1">
                                Técnico
                            </label>
                            <select name="tecnico_id"
                                class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-sm text-zinc-900 dark:text-zinc-100 focus:ring focus:border-blue-500">
                                <option value="">Todos</option>
                                @foreach ($tecnicos as $t)
                                    <option value="{{ $t->id }}" @selected(request('tecnico_id') == $t->id)>
                                        {{ $t->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <input type="hidden" name="tecnico_id" value="{{ auth()->id() }}">
                        <div>
                            <label class="block text-sm font-semibold text-zinc-600 dark:text-zinc-300 mb-1">
                                Técnico
                            </label>
                            <input type="text" readonly value="{{ auth()->user()->name }}"
                                class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-zinc-100 dark:bg-zinc-800 text-sm text-zinc-900 dark:text-zinc-100 px-3 py-2">
                        </div>
                    @endif

                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-zinc-600 dark:text-zinc-300 mb-1">
                            Repuesto
                        </label>
                        <select name="repuesto_id"
                            class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-sm text-zinc-900 dark:text-zinc-100 focus:ring focus:border-blue-500">
                            <option value="">Todos</option>
                            @foreach ($repuestos as $r)
                                @php $meta = $r->serie ?: ($r->modelo ?: ($r->marca ?: null)); @endphp
                                <option value="{{ $r->id }}" @selected(request('repuesto_id') == $r->id)>
                                    {{ $r->nombre }} @if ($meta)
                                        ({{ $meta }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-zinc-600 dark:text-zinc-300 mb-1">
                            Estado
                        </label>
                        <select name="estado"
                            class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-sm text-zinc-900 dark:text-zinc-100 focus:ring focus:border-blue-500">
                            <option value="">Todos</option>
                            @foreach ($estados as $e)
                                <option value="{{ $e['v'] }}" @selected(request('estado') === $e['v'])>
                                    {{ $e['t'] }}
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
                        <a href="{{ route('invtecnico.index') }}"
                            class="inline-flex justify-center items-center gap-2 px-4 py-2 rounded-lg border border-zinc-300 dark:border-zinc-700 text-sm font-semibold text-zinc-700 dark:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800">
                            Limpiar
                        </a>
                    </div>
                </form>
            </div>

            {{-- Totales --}}
            @if ($totalesPorTecnico->isNotEmpty())
                <div
                    class="rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-sm p-5">
                    <p class="text-sm font-semibold text-zinc-600 dark:text-zinc-300 mb-3">
                        Totales por técnico
                    </p>
                    <div class="flex flex-wrap gap-3">
                        @foreach ($totalesPorTecnico as $tecId => $row)
                            <div
                                class="px-4 py-2 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-sm text-zinc-700 dark:text-zinc-100">
                                <b>{{ optional($tecnicos->firstWhere('id', $tecId))->name ?? 'Técnico ' . $tecId }}:</b>
                                {{ $row->total }} unidad(es)
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Tabla --}}
            <div
                class="rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800 text-sm">
                        <thead class="bg-zinc-50 dark:bg-zinc-800/70 text-zinc-600 dark:text-zinc-300">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold">Técnico</th>
                                <th class="px-4 py-3 text-left font-semibold">Repuesto</th>
                                <th class="px-4 py-3 text-right font-semibold">Cantidad</th>
                                <th class="px-4 py-3 text-left font-semibold">Estado</th>
                                <th class="px-4 py-3 text-left font-semibold">Observación</th>
                                <th class="px-4 py-3 text-left font-semibold">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 text-zinc-800 dark:text-zinc-100">
                            @forelse ($items as $it)
                                @php $meta = $it->repuesto->serie ?: ($it->repuesto->modelo ?: ($it->repuesto->marca ?: null)); @endphp
                                <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition">
                                    <td class="px-4 py-3">
                                        <div class="font-semibold">{{ $it->tecnico->name ?? '—' }}</div>
                                        <div class="text-xs text-zinc-500">ID: {{ $it->tecnico_id }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-semibold">{{ $it->repuesto->nombre }}</div>
                                        @if ($meta)
                                            <div class="text-xs text-zinc-500">{{ $meta }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right font-semibold">
                                        {{ number_format($it->cantidad, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3">
                                        @if ($it->estado === 'asignado')
                                            <span
                                                class="px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 dark:bg-amber-950/40 dark:text-amber-200">
                                                Asignado
                                            </span>
                                        @else
                                            <span
                                                class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-200">
                                                Devuelto
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        {{ $it->observacion ?: '—' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        @if ($it->estado === 'asignado' && $it->cantidad > 0)
                                            <form action="{{ route('invtecnico.devolver', $it->id) }}" method="POST"
                                                class="flex flex-col sm:flex-row gap-2">
                                                @csrf
                                                <input type="number" name="cantidad" min="1"
                                                    max="{{ $it->cantidad }}" value="1"
                                                    class="w-24 rounded-lg border border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 px-3 py-1 text-sm">
                                                <input type="text" name="observacion"
                                                    placeholder="Observación (opcional)"
                                                    class="flex-1 rounded-lg border border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 px-3 py-1 text-sm">
                                                <button type="submit"
                                                    class="inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold">
                                                    <i class="fa fa-rotate-left text-xs"></i>
                                                    Devolver
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-xs text-zinc-500">Sin acciones</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-10 text-center text-zinc-500">
                                        No hay movimientos que cumplan los filtros.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-4 py-3 border-t border-zinc-200 dark:border-zinc-800">
                    {{ $items->links() }}
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
