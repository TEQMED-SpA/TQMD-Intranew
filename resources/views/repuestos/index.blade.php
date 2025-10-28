<x-layouts.app :title="$title ?? 'Repuestos'">
    <div class="min-h-screen px-4 py-8 bg-zinc-50 dark:bg-zinc-800 transition-all">
        <div class="max-w-7xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-zinc-800 dark:text-white">Repuestos</h1>
                <a href="{{ route('repuestos.create') }}"
                    class="bg-zinc-200 dark:bg-zinc-700 hover:bg-zinc-300
                    dark:hover:bg-zinc-600 text-zinc-800 dark:text-white font-semibold px-6 py-2 rounded-lg transition">
                    <i class="fa fa-plus"></i>
                    Nuevo Repuesto
                </a>
            </div>

            <!-- Filtros -->
            <div class="mb-6 bg-white dark:bg-zinc-900 rounded-lg shadow p-4">
                <form method="GET" action="{{ route('repuestos.index') }}" class="flex gap-4 flex-wrap">
                    <div class="flex-1 min-w-[200px]">
                        <input type="text" name="buscar" value="{{ request('buscar') }}"
                            placeholder="Buscar por nombre, modelo, marca..."
                            class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="min-w-[150px]">
                        <select name="categoria"
                            class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                            <option value="">Todas las categorías</option>
                            @foreach (\App\Models\CategoriaRepuesto::all() as $cat)
                                <option value="{{ $cat->id }}"
                                    {{ request('categoria') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="min-w-[150px]">
                        <select name="stock"
                            class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                            <option value="">Todos los stocks</option>
                            <option value="bajo" {{ request('stock') == 'bajo' ? 'selected' : '' }}>Stock bajo (&lt;
                                10)</option>
                            <option value="medio" {{ request('stock') == 'medio' ? 'selected' : '' }}>Stock medio
                                (10-50)</option>
                            <option value="alto" {{ request('stock') == 'alto' ? 'selected' : '' }}>Stock alto (&gt;
                                50)</option>
                        </select>
                    </div>
                    <button type="submit"
                        class="bg-blue-500 dark:bg-zinc-600 hover:bg-zinc-400 dark:hover:bg-zinc-800 text-zinc-300 dark:text-white font-semibold px-6 py-2 rounded-lg transition"
                        title="buscar">
                        <i class="fa fa-search"></i>
                    </button>
                    <a href="{{ route('repuestos.index') }}"
                        class="bg-zinc-200 dark:bg-zinc-700 hover:bg-zinc-300 dark:hover:bg-zinc-600 text-zinc-800 dark:text-white font-semibold px-6 py-2 rounded-lg transition"
                        title="limpiar">
                        <i class="fa fa-refresh"></i>
                    </a>
                    <a href="{{ route('repuestos.baja') }}"
                        class="bg-red-500 hover:bg-red-700 text-white font-semibold px-6 py-2 rounded-lg transition">
                        <i class="fa fa-exclamation-triangle"></i> De Baja
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
                            <th class="p-3 text-zinc-700 dark:text-white font-semibold">Nombre</th>
                            <th class="p-3 text-zinc-700 dark:text-white font-semibold">Modelo</th>
                            <th class="p-3 text-zinc-700 dark:text-white font-semibold">Marca</th>
                            <th class="p-3 text-zinc-700 dark:text-white font-semibold">Ubicación</th>
                            <th class="p-3 text-zinc-700 dark:text-white font-semibold">Estado</th>
                            <th class="p-3 text-zinc-700 dark:text-white font-semibold">Stock</th>
                            <th class="p-3 text-zinc-700 dark:text-white font-semibold">Categoría</th>
                            <th class="p-3 text-center text-zinc-700 dark:text-white font-semibold">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($repuestos as $repuesto)
                            <tr class="{{ $tableRowClass }}">
                                <td class="p-3 text-zinc-900 dark:text-zinc-100 font-medium">
                                    {{ $repuesto->nombre }}
                                </td>
                                <td class="p-3 text-zinc-900 dark:text-zinc-300">{{ $repuesto->modelo }}</td>
                                <td class="p-3 text-zinc-900 dark:text-zinc-300">{{ $repuesto->marca }}</td>
                                <td class="p-3 text-zinc-900 dark:text-zinc-300">{{ $repuesto->ubicacion ?? 'N/A' }}
                                </td>
                                <td class="p-3 text-zinc-900 dark:text-zinc-300">
                                    {{ $repuesto->estado?->nombre ?? 'Desconocido' }}
                                </td>
                                <td class="p-3">
                                    @php
                                        $stockClass = '';
                                        if ($repuesto->stock < 10) {
                                            $stockClass =
                                                'bg-red-100 dark:bg-red-900/40 text-red-800 dark:text-red-200';
                                        } elseif ($repuesto->stock < 50) {
                                            $stockClass =
                                                'bg-yellow-100 dark:bg-yellow-900/40 text-yellow-800 dark:text-yellow-200';
                                        } else {
                                            $stockClass =
                                                'bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-200';
                                        }
                                    @endphp
                                    <span
                                        class="inline-block rounded-full px-3 py-1 text-xs font-semibold {{ $stockClass }}">
                                        {{ $repuesto->stock }} uds.
                                    </span>
                                </td>
                                <td class="p-3 text-zinc-900 dark:text-zinc-300">
                                    {{ $repuesto->categoria?->nombre ?? 'Sin categoría' }}
                                </td>
                                <td class="p-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('repuestos.show', $repuesto) }}"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-500 hover:bg-blue-600 text-white transition-colors duration-200"
                                            title="Ver">
                                            <i class="fa fa-eye text-sm"></i>
                                        </a>
                                        <a href="{{ route('repuestos.edit', $repuesto) }}"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-green-500 hover:bg-green-600 text-white transition-colors duration-200"
                                            title="Editar">
                                            <i class="fa fa-pencil text-sm"></i>
                                        </a>
                                        @if (auth()->user() && auth()->user()->tienePrivilegio('crear_solicitudes'))
                                            <a href="{{ route('solicitudes.create', ['repuesto_id' => $repuesto->id]) }}"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-yellow-500 hover:bg-yellow-600 text-white transition-colors duration-200"
                                                title="Solicitar repuesto">
                                                <i class="fa fa-paper-plane text-sm"></i>
                                            </a>
                                        @endif
                                        <form action="{{ route('repuestos.destroy', $repuesto) }}" method="POST"
                                            style="display:inline;" onsubmit="return confirm('¿Eliminar repuesto?');">
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
                                    No se encontraron repuestos
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-4 bg-white dark:bg-zinc-800">
                    {{ $repuestos->links() }}
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
