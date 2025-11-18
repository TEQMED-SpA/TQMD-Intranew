<x-layouts.app :title="'Equipos'">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-zinc-800 dark:text-white">Equipos</h1>
            <a href="{{ route('equipos.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                Agregar Equipo
            </a>
        </div>

        {{-- Filtros --}}
        <form method="GET"
            class="grid md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6 bg-white dark:bg-zinc-900 p-4 rounded-lg shadow">
            <div class="col-span-3 lg:col-span-2">
                <label class="block text-sm mb-1 text-zinc-700 dark:text-zinc-300">Cliente</label>
                <select name="cliente_id" id="cliente_id"
                    class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg
                               bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos</option>
                    @foreach ($clientes as $c)
                        <option value="{{ $c->id }}" @selected(request('cliente_id') == $c->id)>
                            {{ $c->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-span-3 lg:col-span-2">
                <label class="block text-sm mb-1 text-zinc-700 dark:text-zinc-300">Centro Médico</label>
                <select name="centro_medico_id" id="centro_medico_id"
                    class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg
                               bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos</option>
                    @foreach ($centros_medicos as $cm)
                        <option value="{{ $cm->id }}" data-cliente="{{ $cm->cliente_id }}"
                            @selected(request('centro_medico_id') == $cm->id)>
                            {{ $cm->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-span-3 lg:col-span-1">
                <label class="block text-sm mb-1 text-zinc-700 dark:text-zinc-300">Estado</label>
                <select name="estado"
                    class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg
                               bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos</option>
                    @foreach (['Operativo', 'En observacion', 'Fuera de servicio', 'Baja'] as $e)
                        <option value="{{ $e }}" @selected(request('estado') === $e)>{{ $e }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-span-3 lg:col-span-1">
                <label class="block text-sm mb-1 text-zinc-700 dark:text-zinc-300">Tipo mantención</label>
                <select name="tipo_mantencion"
                    class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg
                               bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos</option>
                    @foreach (['T1', 'T2', 'T3'] as $t)
                        <option value="{{ $t }}" @selected(request('tipo_mantencion') == $t)>{{ $t }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-span-3 lg:col-span-1">
                <label class="block text-sm mb-1 text-zinc-700 dark:text-zinc-300">Estado mantención</label>
                <select name="estado_mantencion"
                    class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg
                               bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos</option>
                    <option value="vencida" @selected(request('estado_mantencion') == 'vencida')>Vencida</option>
                    <option value="proxima" @selected(request('estado_mantencion') == 'proxima')>Próxima (30 días)</option>
                    <option value="aldia" @selected(request('estado_mantencion') == 'aldia')>Al día</option>
                </select>
            </div>

            <div class="col-span-3 lg:col-span-1 flex items-end gap-2">
                <button class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    Filtrar
                </button>
                <a href="{{ route('equipos.index') }}"
                    class="w-full text-center bg-zinc-200 dark:bg-zinc-700 hover:bg-zinc-300 dark:hover:bg-zinc-600
                          text-zinc-800 dark:text-white px-4 py-2 rounded-lg">
                    Limpiar
                </a>
            </div>
        </form>

        {{-- Tabla de equipos --}}
        <div class="overflow-x-auto rounded-lg shadow bg-white dark:bg-zinc-900">
            <table
                class="w-full table-auto text-left text-sm border-collapse border border-zinc-200 dark:border-zinc-700">
                <thead>
                    <tr class="bg-white dark:bg-zinc-700 border-b border-zinc-300 dark:border-zinc-600">
                        <th
                            class="p-3 text-zinc-700 dark:text-white font-semibold border-r border-zinc-300 dark:border-zinc-600">
                            Código</th>
                        <th
                            class="p-3 text-zinc-700 dark:text-white font-semibold border-r border-zinc-300 dark:border-zinc-600">
                            Nombre</th>
                        <th
                            class="p-3 text-zinc-700 dark:text-white font-semibold border-r border-zinc-300 dark:border-zinc-600">
                            Modelo</th>
                        <th
                            class="p-3 text-zinc-700 dark:text-white font-semibold border-r border-zinc-300 dark:border-zinc-600">
                            Centro</th>
                        <th
                            class="p-3 text-zinc-700 dark:text-white font-semibold border-r border-zinc-300 dark:border-zinc-600">
                            Tipo mantención</th>
                        <th
                            class="p-3 text-zinc-700 dark:text-white font-semibold border-r border-zinc-300 dark:border-zinc-600">
                            Próx. Mantención</th>
                        <th class="p-3 text-center text-zinc-700 dark:text-white font-semibold">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($equipos as $equipo)
                        @php
                            $fecha = $equipo->proxima_mantencion;
                            $dias = $fecha ? now()->diffInDays($fecha, false) : null;
                            if (is_null($fecha)) {
                                $estadoMantencion = [
                                    '-',
                                    'bg-zinc-200 dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200',
                                ];
                            } elseif ($dias < 0) {
                                $estadoMantencion = ['Vencida', 'bg-red-600 text-white'];
                            } elseif ($dias <= 30) {
                                $estadoMantencion = ['Próxima', 'bg-yellow-500 text-black'];
                            } else {
                                $estadoMantencion = ['Al día', 'bg-green-600 text-white'];
                            }
                        @endphp
                        <tr
                            class="even:bg-zinc-100 odd:bg-white dark:even:bg-zinc-800 dark:odd:bg-zinc-900
                                   border-b border-zinc-200 dark:border-zinc-700
                                   transition-all hover:bg-zinc-200/40 dark:hover:bg-zinc-700/30">
                            <td
                                class="p-3 border-r border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 font-medium">
                                {{ $equipo->codigo ?? '—' }}
                            </td>
                            <td
                                class="p-3 border-r border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100">
                                {{ $equipo->nombre }}
                            </td>
                            <td
                                class="p-3 border-r border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-zinc-300">
                                {{ $equipo->modelo ?? '—' }}
                            </td>
                            <td
                                class="p-3 border-r border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-zinc-300">
                                {{ $equipo->centro?->nombre ?? '—' }}
                            </td>
                            <td
                                class="p-3 border-r border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-zinc-300">
                                {{ $equipo->tipo_mantencion ?? '—' }}
                            </td>
                            <td class="p-3 border-r border-zinc-200 dark:border-zinc-700">
                                <span class="px-2 py-1 rounded {{ $estadoMantencion[1] }}">
                                    {{ $fecha ? \Carbon\Carbon::parse($fecha)->format('d-m-Y') : '—' }}
                                </span>
                                <small class="block text-xs mt-1 text-zinc-500 dark:text-zinc-400">
                                    {{ $estadoMantencion[0] }}
                                </small>
                            </td>
                            <td class="p-3 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('equipos.show', $equipo) }}"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-500 hover:bg-blue-600 text-white transition-colors duration-200"
                                        title="Ver">
                                        <i class="fa fa-eye text-sm"></i>
                                    </a>
                                    <a href="{{ route('equipos.edit', $equipo) }}"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-green-500 hover:bg-green-600 text-white transition-colors duration-200"
                                        title="Editar">
                                        <i class="fa fa-pencil text-sm"></i>
                                    </a>
                                    <form action="{{ route('equipos.destroy', $equipo) }}" method="POST"
                                        onsubmit="return confirm('¿Eliminar equipo?')" style="display:inline;">
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
                            <td colspan="7" class="p-6 text-center text-zinc-500 dark:text-zinc-400">
                                No se encontraron equipos
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-4 bg-white dark:bg-zinc-800">
                {{ $equipos->withQueryString()->links() }}
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
      document.addEventListener('DOMContentLoaded', () => {
        const clienteSel = document.getElementById('cliente_id');
        const centroSel  = document.getElementById('centro_medico_id');
        const opciones   = Array.from(centroSel.querySelectorAll('option'));

        function filtrar() {
            const cid = clienteSel.value;
            centroSel.innerHTML = '<option value="">Todos</option>';
            opciones.forEach(o => {
                if (!o.dataset.cliente || !cid || o.dataset.cliente === cid) {
                    centroSel.appendChild(o);
                }
            });
        }
        clienteSel?.addEventListener('change', filtrar);
        // Si ya viene seleccionado un cliente, filtra de entrada
        if (clienteSel.value) filtrar();
      });
    </script>
    @endpush
</x-layouts.app>
