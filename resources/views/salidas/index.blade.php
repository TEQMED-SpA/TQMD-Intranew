<x-layouts.app :title="$title ?? 'Salidas'">
    <div class="min-h-screen px-4 py-8 bg-zinc-50 dark:bg-zinc-800 transition-all">
        <div class="max-w-6xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-zinc-800 dark:text-white">Salidas</h1>
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
                            <th class="p-3 text-zinc-700 dark:text-white font-semibold">Fecha</th>
                            <th class="p-3 text-zinc-700 dark:text-white font-semibold">Producto</th>
                            <th class="p-3 text-zinc-700 dark:text-white font-semibold">Cantidad</th>
                            <th class="p-3 text-zinc-700 dark:text-white font-semibold">Destino</th>
                            <th class="p-3 text-zinc-700 dark:text-white font-semibold">Estado</th>
                            <th class="p-3 text-center text-zinc-700 dark:text-white font-semibold">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($salidas as $salida)
                            <tr class="{{ $tableRowClass }}">
                                <td class="p-3 text-zinc-900 dark:text-zinc-100 font-medium">{{ $salida->fecha }}</td>
                                <td class="p-3 text-zinc-900 dark:text-zinc-300">{{ $salida->producto?->nombre }}</td>
                                <td class="p-3 text-zinc-900 dark:text-zinc-300">{{ $salida->cantidad }}</td>
                                <td class="p-3 text-zinc-900 dark:text-zinc-300">{{ $salida->destino }}</td>
                                <td class="p-3">
                                    @php
                                        $isActive = (int) $salida->estado === 1;
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
                                        <a href="{{ route('salidas.show', $salida) }}"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-500 hover:bg-blue-600 text-white transition-colors duration-200"
                                            title="Ver">
                                            <i class="fa fa-eye text-sm"></i>
                                        </a>
                                        <a href="{{ route('salidas.edit', $salida) }}"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-green-500 hover:bg-green-600 text-white transition-colors duration-200"
                                            title="Editar">
                                            <i class="fa fa-pencil text-sm"></i>
                                        </a>
                                        <form action="{{ route('salidas.destroy', $salida) }}" method="POST"
                                            style="display:inline;" onsubmit="return confirm('¿Eliminar salida?');">
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
                    {{ $salidas->links() }}
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
