<x-layouts.app :title="$title ?? 'Centros Médicos'">
    <div class="min-h-screen px-4 py-8 bg-zinc-50 dark:bg-zinc-800 transition-all">
        <div class="max-w-6xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-zinc-800 dark:text-white">Centros Médicos</h1>

                <a href="{{ route('centros_medicos.create') }}">
                    <button type="button"
                        class="bg-[#00618E] text-white font-semibold px-4 py-2 rounded-lg hover:bg-[#004a6b] transition flex items-center gap-2">
                        <i class="fa fa-plus"></i>
                        Nuevo Centro
                    </button>
                </a>
            </div>

            <div class="overflow-x-auto rounded-lg shadow bg-blue-100 dark:bg-zinc-900">
                @php
                    $tableRowClass = 'border-transparent hover:border-zinc-200 dark:hover:border-transparent 
                                       bg-zinc-50 dark:bg-zinc-900 
                                       hover:bg-zinc-800/5 dark:hover:bg-white/[7%] 
                                       text-zinc-500 dark:text-white/80 
                                       hover:text-zinc-800 dark:hover:text-white 
                                       transition-all duration-200';
                @endphp

                <table class="w-full table-auto text-left text-sm">
                    <thead>
                        <tr class="bg-white dark:bg-zinc-700">
                            <th class="p-3 text-zinc-900 dark:text-white font-semibold">Nombre</th>
                            <th class="p-3 text-zinc-900 dark:text-white font-semibold">Dirección</th>
                            <th class="p-3 text-zinc-900 dark:text-white font-semibold">Teléfono</th>
                            <th class="p-3 text-zinc-900 dark:text-white font-semibold">Estado</th>
                            <th class="p-3 text-center text-zinc-900 dark:text-white font-semibold">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($centros_medicos as $centro)
                            <tr class="{{ $tableRowClass }}">
                                <td class="p-3 text-zinc-900 dark:text-zinc-100 font-medium">{{ $centro->nombre }}</td>
                                <td class="p-3 text-zinc-700 dark:text-zinc-300">{{ $centro->direccion ?? '—' }}</td>
                                <td class="p-3 text-zinc-700 dark:text-zinc-300">{{ $centro->telefono ?? '—' }}</td>
                                <td class="p-3">
                                    @if ($centro->activo)
                                        <span
                                            class="inline-block rounded-full px-3 py-1 text-xs font-semibold
                                            bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-200">
                                            Activo
                                        </span>
                                    @else
                                        <span
                                            class="inline-block rounded-full px-3 py-1 text-xs font-semibold
                                            bg-red-100 dark:bg-red-900/40 text-red-800 dark:text-red-200">
                                            Inactivo
                                        </span>
                                    @endif
                                </td>

                                <td class="p-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('centros_medicos.show', ['centros_medico' => $centro->id]) }}"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-500 hover:bg-blue-600 text-white transition-colors duration-200"
                                            title="Ver">
                                            <i class="fa fa-eye text-sm"></i>
                                        </a>

                                        @can('editar_centros_medicos')
                                            <a href="{{ route('centros_medicos.edit', ['centros_medico' => $centro->id]) }}"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-green-500 hover:bg-green-600 text-white transition-colors duration-200"
                                                title="Editar">
                                                <i class="fa fa-pencil text-sm"></i>
                                            </a>

                                            <form
                                                action="{{ route('centros_medicos.destroy', ['centros_medico' => $centro->id]) }}"
                                                method="POST" style="display:inline;"
                                                onsubmit="return confirm('¿Eliminar centro?');">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-500 hover:bg-red-600 text-white transition-colors duration-200"
                                                    title="Eliminar">
                                                    <i class="fa fa-trash text-sm"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="p-4 bg-white dark:bg-zinc-800">
                    {{ $centros_medicos->links() }}
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
