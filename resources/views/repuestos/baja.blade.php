<x-layouts.app title="Repuestos Dados de Baja">
    <div class="min-h-screen px-4 py-8 bg-zinc-50 dark:bg-zinc-800 transition-all">
        <div class="max-w-7xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-red-700 dark:text-red-400">
                    <i class="fa fa-exclamation-triangle"></i> Repuestos Dados de Baja
                </h1>
                <a href="{{ route('repuestos.index') }}"
                    class="bg-zinc-200 dark:bg-zinc-700 hover:bg-zinc-300 dark:hover:bg-zinc-600 text-zinc-800 dark:text-white font-semibold px-6 py-2 rounded-lg transition">
                    <i class="fa fa-arrow-left"></i> Volver a Repuestos
                </a>
            </div>

            <div class="overflow-x-auto rounded-lg shadow bg-white dark:bg-zinc-900">
                <table class="w-full table-auto text-left text-sm">
                    <thead>
                        <tr class="bg-zinc-100 dark:bg-zinc-700">
                            <th class="p-3 text-zinc-700 dark:text-white font-semibold">Nombre</th>
                            <th class="p-3 text-zinc-700 dark:text-white font-semibold">Modelo</th>
                            <th class="p-3 text-zinc-700 dark:text-white font-semibold">Marca</th>
                            <th class="p-3 text-zinc-700 dark:text-white font-semibold">Ubicación</th>
                            <th class="p-3 text-zinc-700 dark:text-white font-semibold">Categoría</th>
                            <th class="p-3 text-center text-zinc-700 dark:text-white font-semibold">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($repuestos as $repuesto)
                            <tr
                                class="border-b border-zinc-200 dark:border-zinc-700 hover:bg-red-200 dark:hover:bg-red-200/20 transition">
                                <td class="p-3 text-zinc-900 dark:text-zinc-100 font-medium">{{ $repuesto->nombre }}
                                </td>
                                <td class="p-3 text-zinc-900 dark:text-zinc-300">{{ $repuesto->modelo }}</td>
                                <td class="p-3 text-zinc-900 dark:text-zinc-300">{{ $repuesto->marca }}</td>
                                <td class="p-3 text-zinc-900 dark:text-zinc-300">{{ $repuesto->ubicacion ?? 'N/A' }}
                                </td>
                                <td class="p-3 text-zinc-900 dark:text-zinc-300">
                                    {{ $repuesto->categoria?->nombre ?? 'Sin categoría' }}</td>
                                <td class="p-3 text-center">
                                    <a href="{{ route('repuestos.show', $repuesto) }}"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-500 hover:bg-blue-600 text-white transition-colors duration-200"
                                        title="Ver">
                                        <i class="fa fa-eye text-sm"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-6 text-center text-zinc-500 dark:text-zinc-400">
                                    No hay repuestos dados de baja.
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
