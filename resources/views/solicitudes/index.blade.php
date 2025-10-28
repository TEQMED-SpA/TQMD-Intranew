<x-layouts.app :title="$title ?? 'Solicitudes'">
    @php
        /** Evita "Undefined variable $estados" si el controlador no la pasa */
        $estados = $estados ?? collect(); // o []
    @endphp

    <div class="min-h-screen px-4 py-8 bg-zinc-50 dark:bg-zinc-800 transition-all">
        <div class="max-w-7xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-zinc-800 dark:text-white">Solicitudes de Repuestos</h1>
                @if (auth()->user() && auth()->user()->tienePrivilegio('crear_solicitudes'))
                    <a href="{{ route('solicitudes.create') }}">
                        <i class="fa fa-plus"></i> Nueva Solicitud
                    </a>
                @endif
            </div>

            <!-- Filtros -->
            <div class="mb-6 bg-white dark:bg-zinc-900 rounded-lg shadow p-4">
                <form method="GET" action="{{ route('solicitudes.index') }}" class="flex gap-4 flex-wrap">
                    <div class="flex-1 min-w-[200px]">
                        <input type="text" name="buscar" value="{{ request('buscar') }}"
                            placeholder="Buscar por número o razón..."
                            class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="min-w-[150px]">
                        <select name="estado"
                            class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                            <option value="">Todos los estados</option>
                            @foreach ($estados as $id => $nombre)
                                <option value="{{ $id }}" {{ request('estado') == $id ? 'selected' : '' }}>
                                    {{ $nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="min-w-[150px]">
                        <select name="clinica"
                            class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                            <option value="">Todos los centros</option>
                            @foreach ($centros as $centro)
                                <option value="{{ $centro->id }}"
                                    {{ request('clinica') == $centro->id ? 'selected' : '' }}>
                                    {{ $centro->centro_dialisis }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit"
                        class="bg-blue-500 dark:bg-zinc-600 hover:bg-zinc-400 dark:hover:bg-zinc-800 text-zinc-300 dark:text-white font-semibold px-6 py-2 rounded-lg transition"
                        title="buscar">
                        <i class="fa fa-search"></i>
                    </button>
                    <a href="{{ route('solicitudes.index') }}"
                        class="bg-zinc-200 dark:bg-zinc-700 hover:bg-zinc-300 dark:hover:bg-zinc-600 text-zinc-800 dark:text-white font-semibold px-6 py-2 rounded-lg transition"
                        title="limpiar">
                        <i class="fa fa-refresh"></i>
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
                            <th class="p-3 text-zinc-700 dark:text-white font-semibold">Número</th>
                            <th class="p-3 text-zinc-700 dark:text-white font-semibold">Fecha</th>
                            <th class="p-3 text-zinc-700 dark:text-white font-semibold">Técnico</th>
                            <th class="p-3 text-zinc-700 dark:text-white font-semibold">Centro Médico</th>
                            <th class="p-3 text-zinc-700 dark:text-white font-semibold">Estado</th>
                            <th class="p-3 text-center text-zinc-700 dark:text-white font-semibold">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($solicitudes as $solicitud)
                            <tr class="{{ $tableRowClass }}">
                                <td class="p-3 text-zinc-900 dark:text-zinc-100 font-medium">
                                    {{ $solicitud->numero_solicitud }}
                                </td>
                                <td class="p-3 text-zinc-900 dark:text-zinc-300">
                                    {{ $solicitud->fecha_solicitud->format('d/m/Y') }}
                                </td>
                                <td class="p-3 text-zinc-900 dark:text-zinc-300">
                                    {{ $solicitud->tecnico->name }}
                                </td>
                                <td class="p-3 text-zinc-900 dark:text-zinc-300">
                                    {{ $solicitud->clinica->centro_dialisis }}
                                </td>
                                <td class="p-3">
                                    @php
                                        $estadoClass = match ($solicitud->estado_id) {
                                            1
                                                => 'bg-yellow-100 dark:bg-yellow-900/40 text-yellow-800 dark:text-yellow-200',
                                            2 => 'bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-200',
                                            3 => 'bg-red-100 dark:bg-red-900/40 text-red-800 dark:text-red-200',
                                            default
                                                => 'bg-gray-100 dark:bg-gray-900/40 text-gray-800 dark:text-gray-200',
                                        };
                                    @endphp
                                    <span
                                        class="inline-block rounded-full px-3 py-1 text-xs font-semibold {{ $estadoClass }}">
                                        {{ $solicitud->estado->nombre }}
                                    </span>
                                </td>
                                <td class="p-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('solicitudes.show', $solicitud) }}"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-500 hover:bg-blue-600 text-white transition-colors duration-200"
                                            title="Ver">
                                            <i class="fa fa-eye text-sm"></i>
                                        </a>
                                        @if ($solicitud->estado_id === 1)
                                            @can('aprobar_solicitudes')
                                                <button onclick="confirmarAprobacion('{{ $solicitud->id }}')"
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-green-500 hover:bg-green-600 text-white transition-colors duration-200"
                                                    title="Aprobar">
                                                    <i class="fa fa-check text-sm"></i>
                                                </button>
                                            @endcan
                                            @can('rechazar_solicitudes')
                                                <button onclick="mostrarFormularioRechazo('{{ $solicitud->id }}')"
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-500 hover:bg-red-600 text-white transition-colors duration-200"
                                                    title="Rechazar">
                                                    <i class="fa fa-times text-sm"></i>
                                                </button>
                                            @endcan
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-6 text-center text-zinc-500 dark:text-zinc-400">
                                    No se encontraron solicitudes
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-4 bg-white dark:bg-zinc-800">
                    {{ $solicitudes->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Rechazo -->
    <div id="modal-rechazo" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title"
        role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-zinc-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white dark:bg-zinc-900 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="form-rechazo" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="px-4 pt-5 pb-4 sm:p-6">
                        <div class="mt-3 text-center sm:mt-0 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-zinc-900 dark:text-white" id="modal-title">
                                Motivo del Rechazo
                            </h3>
                            <div class="mt-2">
                                <textarea name="motivo_rechazo" id="motivo_rechazo" rows="4"
                                    class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-blue-500"
                                    required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse bg-zinc-50 dark:bg-zinc-800">
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Rechazar
                        </button>
                        <button type="button" onclick="cerrarModalRechazo()"
                            class="mt-3 w-full inline-flex justify-center rounded-lg border border-zinc-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-zinc-700 hover:bg-zinc-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function confirmarAprobacion(id) {
                if (confirm('¿Está seguro que desea aprobar esta solicitud?')) {
                    let form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/solicitudes/${id}/aprobar`;
                    form.innerHTML = `@csrf @method('PUT')`;
                    document.body.appendChild(form);
                    form.submit();
                }
            }

            function mostrarFormularioRechazo(id) {
                const modal = document.getElementById('modal-rechazo');
                const form = document.getElementById('form-rechazo');
                form.action = `/solicitudes/${id}/rechazar`;
                modal.classList.remove('hidden');
            }

            function cerrarModalRechazo() {
                const modal = document.getElementById('modal-rechazo');
                modal.classList.add('hidden');
                document.getElementById('motivo_rechazo').value = '';
            }
        </script>
    @endpush
</x-layouts.app>
