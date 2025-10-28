<x-layouts.app :title="'Centro: ' . ($centroMedico->nombre ?? '—')">
    <div class="min-h-screen px-4 py-8 bg-zinc-50 dark:bg-zinc-800 transition-all">
        <div class="max-w-5xl mx-auto">
            {{-- Header --}}
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">
                    {{ $centroMedico->nombre ?? 'Centro Médico' }}
                </h1>
                <div class="flex gap-2">
                    @role('admin|auditor')
                        <a href="{{ route('centros_medicos.edit', $centroMedico) }}"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition">
                            Editar
                        </a>
                    @endrole
                    <a href="{{ route('centros_medicos.index') }}"
                        class="border border-zinc-300 dark:border-zinc-700 text-zinc-800 dark:text-zinc-100 px-4 py-2 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-700 transition">
                        Volver
                    </a>
                </div>
            </div>

            {{-- Card de detalles --}}
            <div class="rounded-lg shadow bg-white dark:bg-zinc-900 p-6 mb-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <div class="text-zinc-500 dark:text-zinc-400">Cliente</div>
                        <div class="font-medium text-zinc-900 dark:text-zinc-100">
                            {{ $centroMedico->cliente->nombre ?? '—' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-zinc-500 dark:text-zinc-400">Teléfono</div>
                        <div class="font-medium text-zinc-900 dark:text-zinc-100">
                            {{ $centroMedico->telefono ?? '—' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-zinc-500 dark:text-zinc-400">Ciudad</div>
                        <div class="font-medium text-zinc-900 dark:text-zinc-100">
                            {{ $centroMedico->ciudad ?? '—' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-zinc-500 dark:text-zinc-400">Región</div>
                        <div class="font-medium text-zinc-900 dark:text-zinc-100">
                            {{ $centroMedico->region ?? '—' }}
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <div class="text-zinc-500 dark:text-zinc-400">Dirección</div>
                        <div class="font-medium text-zinc-900 dark:text-zinc-100">
                            {{ $centroMedico->direccion ?? '—' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-zinc-500 dark:text-zinc-400">Estado</div>
                        <div class="font-medium">
                            @if ($centroMedico->activo)
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
                        </div>
                    </div>

                    <div>
                        <div class="text-zinc-500 dark:text-zinc-400">Código Cliente</div>
                        <div class="font-medium text-zinc-900 dark:text-zinc-100">
                            {{ $centroMedico->cod_cliente ?? '—' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-zinc-500 dark:text-zinc-400">Código Centro</div>
                        <div class="font-medium text-zinc-900 dark:text-zinc-100">
                            {{ $centroMedico->cod_centro_medico ?? '—' }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Máquinas del centro --}}
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">
                    Máquinas del centro
                    <span class="text-sm font-normal text-zinc-500 dark:text-zinc-400">
                        ({{ $centroMedico->equipos?->count() ?? 0 }})
                    </span>
                </h2>
                @role('admin|auditor|tecnico')
                    <a href="{{ route('equipos.create') }}"
                        class="bg-zinc-200 dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 px-3 py-2 rounded-lg hover:bg-zinc-300 dark:hover:bg-zinc-600 transition text-sm">
                        Agregar máquina
                    </a>
                @endrole
            </div>

            <div class="rounded-lg overflow-hidden shadow bg-white dark:bg-zinc-900">
                <table class="w-full table-auto text-left text-sm">
                    <thead class="bg-zinc-50 dark:bg-zinc-800">
                        <tr>
                            <th class="p-3 text-zinc-900 dark:text-zinc-100 font-semibold">Nombre</th>
                            <th class="p-3 text-zinc-900 dark:text-zinc-100 font-semibold">Modelo</th>
                            <th class="p-3 text-zinc-900 dark:text-zinc-100 font-semibold">Serie</th>
                            <th class="p-3 text-zinc-900 dark:text-zinc-100 font-semibold">Estado</th>
                            <th class="p-3 text-center text-zinc-900 dark:text-zinc-100 font-semibold">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($centroMedico->equipos ?? [] as $e)
                            <tr
                                class="border-t border-zinc-200 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                                <td class="p-3 text-zinc-800 dark:text-zinc-100">{{ $e->nombre ?? '—' }}</td>
                                <td class="p-3 text-zinc-700 dark:text-zinc-300">{{ $e->modelo ?? '—' }}</td>
                                <td class="p-3 text-zinc-700 dark:text-zinc-300">{{ $e->numero_serie ?? '—' }}</td>
                                <td class="p-3 text-zinc-700 dark:text-zinc-300">{{ $e->estado ?? '—' }}</td>
                                <td class="p-3 text-center">
                                    <a href="{{ route('equipos.show', $e) }}"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-500 hover:bg-blue-600 text-white transition"
                                        title="Ver">
                                        <i class="fa fa-eye text-sm"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-4 text-center text-zinc-500 dark:text-zinc-400">
                                    Sin máquinas registradas en este centro.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
