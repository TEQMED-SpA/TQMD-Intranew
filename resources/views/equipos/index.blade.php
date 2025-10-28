<x-layouts.app :title="'Equipos'">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-zinc-800 dark:text-white">Equipos</h1>
            <a href="{{ route('equipos.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                Agregar Equipo
            </a>
        </div>

        {{-- Filtros --}}
        <form method="GET" class="grid md:grid-cols-3 gap-4 mb-6 bg-zinc-100 dark:bg-zinc-800 p-4 rounded-lg">
            <div class="col-span-12 md:col-span-4">
                <label class="block text-sm mb-1">Cliente</label>
                <select name="cliente_id" id="cliente_id" class="w-full border rounded px-3 py-2">
                    <option value="">Todos</option>
                    @foreach ($clientes as $c)
                        <option value="{{ $c->id }}" @selected(request('cliente_id') == $c->id)>{{ $c->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-span-12 md:col-span-4">
                <label class="block text-sm mb-1">Centro Médico</label>
                <select name="centro_medico_id" id="centro_id" class="w-full border rounded px-3 py-2">
                    <option value="">Todos</option>
                    @foreach ($centros_medicos as $cm)
                        <option value="{{ $cm->id }}" data-cliente="{{ $cm->cliente_id }}"
                            @selected(request('centro_medico_id') == $cm->id)>
                            {{ $cm->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-span-12 md:col-span-2">
                <label class="block text-sm mb-1">Estado</label>
                <select name="estado" class="w-full border rounded px-3 py-2">
                    <option value="">Todos</option>
                    @foreach (['operativo', 'En observación', 'fuera de servicio'] as $e)
                        <option value="{{ $e }}" @selected(request('estado') === $e)>{{ ucfirst($e) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1 text-zinc-700 dark:text-zinc-300">Tipo mantención</label>
                <select name="tipo_mantencion" class="w-full border rounded px-3 py-2">
                    <option value="">Todos</option>
                    @foreach (['T1', 'T2', 'T3', 'T4'] as $t)
                        <option value="{{ $t }}" @selected(request('tipo_mantencion') == $t)>{{ $t }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1 text-zinc-700 dark:text-zinc-300">Estado mantención</label>
                <select name="estado_mantencion" class="w-full border rounded px-3 py-2">
                    <option value="">Todos</option>
                    <option value="vencida" @selected(request('estado_mantencion') == 'vencida')>Vencida</option>
                    <option value="proxima" @selected(request('estado_mantencion') == 'proxima')>Próxima (30 días)</option>
                    <option value="aldia" @selected(request('estado_mantencion') == 'aldia')>Al día</option>
                </select>
            </div>

            <div class="flex items-end">
                <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded w-full">Filtrar</button>
            </div>
        </form>

        {{-- Tabla de equipos --}}
        <div class="overflow-x-auto rounded-lg shadow bg-white dark:bg-zinc-900">
            <table class="w-full table-auto text-left text-sm">
                <thead>
                    <tr class="bg-zinc-200 dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100">
                        <th class="p-3">Nombre</th>
                        <th class="p-3">Modelo</th>
                        <th class="p-3">Centro</th>
                        <th class="p-3">Tipo</th>
                        <th class="p-3">Próx. Mantención</th>
                        <th class="p-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($equipos as $equipo)
                        @php
                            $fecha = $equipo->proxima_mantencion;
                            $dias = $fecha ? now()->diffInDays($fecha, false) : null;
                            if (is_null($fecha)) {
                                $estadoMantencion = [
                                    '-',
                                    'bg-gray-300 dark:bg-gray-700 text-gray-800 dark:text-gray-200',
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
                            class="border-b border-zinc-200 dark:border-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-800">
                            <td class="p-3 font-medium">{{ $equipo->nombre }}</td>
                            <td class="p-3">{{ $equipo->modelo }}</td>
                            <td class="p-3">{{ $equipo->centro?->nombre ?? '—' }}</td>
                            <td class="p-3">{{ $equipo->tipo_mantencion ?? '—' }}</td>
                            <td class="p-3">
                                <span class="px-2 py-1 rounded {{ $estadoMantencion[1] }}">
                                    {{ $fecha ? \Carbon\Carbon::parse($fecha)->format('d-m-Y') : '—' }}
                                </span>
                                <small class="block text-xs mt-1 text-zinc-500">{{ $estadoMantencion[0] }}</small>
                            </td>
                            <td class="p-3 text-center">
                                <a href="{{ route('equipos.show', $equipo) }}"
                                    class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded">
                                    Ver
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="p-4 bg-zinc-50 dark:bg-zinc-800">
                {{ $equipos->withQueryString()->links() }}
            </div>
        </div>
    </div>
</x-layouts.app>
