<x-layouts.app :title="$title ?? 'Repuestos'">
    <div class="min-h-screen px-4 py-8 bg-zinc-50 dark:bg-zinc-800 transition-all">
        <div class="max-w-6xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-zinc-800 dark:text-white">Repuestos</h1>
                <a href="{{ route('repuestos.create') }}">
                    <button type="button"
                        class="bg-zinc-200 dark:bg-zinc-700 text-zinc-100 dark:text-zinc-100 font-semibold px-4 py-2 rounded-lg hover:bg-zinc-300 dark:hover:bg-zinc-600 transition flex items-center gap-2">
                        <i class="fa fa-plus"></i>
                        Nuevo Repuesto
                    </button>
                </a>
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
                            <th class="p-3 text-zinc-700 dark:text-white font-semibold">Serie</th>
                            <th class="p-3 text-zinc-700 dark:text-white font-semibold">Modelo</th>
                            <th class="p-3 text-zinc-700 dark:text-white font-semibold">Marca</th>
                            <th class="p-3 text-zinc-700 dark:text-white font-semibold">Ubicación</th>
                            <th class="p-3 text-zinc-700 dark:text-white font-semibold">Descripción</th>
                            <th class="p-3 text-zinc-700 dark:text-white font-semibold">Stock</th>
                            <th class="p-3 text-zinc-700 dark:text-white font-semibold">Foto</th>
                            <th class="p-3 text-zinc-700 dark:text-white font-semibold">Categoría</th>
                            <th class="p-3 text-zinc-700 dark:text-white font-semibold">Estado</th>
                            <th class="p-3 text-center text-zinc-700 dark:text-white font-semibold">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($repuestos as $repuesto)
                            <tr class="{{ $tableRowClass }}">
                                <td class="p-3 text-zinc-900 dark:text-zinc-100 font-medium">
                                    {{ $repuesto->producto_nombre }}</td>
                                <td class="p-3 text-zinc-900 dark:text-zinc-100">{{ $repuesto->producto_serie }}</td>
                                <td class="p-3 text-zinc-900 dark:text-zinc-300">{{ $repuesto->producto_modelo }}</td>
                                <td class="p-3 text-zinc-900 dark:text-zinc-300">{{ $repuesto->producto_marca }}</td>
                                <td class="p-3 text-zinc-900 dark:text-zinc-300">{{ $repuesto->producto_ubicacion }}
                                </td>
                                <td class="p-3 text-zinc-900 dark:text-zinc-300">{{ $repuesto->producto_descripcion }}
                                </td>
                                <td class="p-3 text-zinc-900 dark:text-zinc-300">{{ $repuesto->producto_stock }}</td>
                                <td class="p-3 text-zinc-900 dark:text-zinc-300">
                                    @if ($repuesto->producto_foto)
                                        <img src="{{ asset('storage/' . $repuesto->producto_foto) }}" alt="Foto"
                                            class="h-8 w-8 object-cover rounded" />
                                    @endif
                                </td>
                                <td class="p-3 text-zinc-900 dark:text-zinc-300">{{ $repuesto->categoria?->nombre }}
                                </td>
                                <td class="p-3">
                                    @php
                                        $isActive = ($repuesto->producto_estado ?? '') === 'activo';
                                    @endphp
                                    <span
                                        class="inline-block rounded-full px-3 py-1 text-xs font-semibold transition-colors duration-200
                                        {{ $isActive
                                            ? 'bg-green-200 dark:bg-green-800 text-green-900 dark:text-zinc-800'
                                            : 'bg-red-200 dark:bg-red-800 text-red-900 dark:text-zinc-800' }}">
                                        {{ $isActive ? 'Activo' : 'Inactivo' }}
                                    </span>
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
                        @endforeach
                    </tbody>
                </table>
                <div class="p-4 bg-white dark:bg-zinc-800">
                    {{ $repuestos->links() }}
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
