@extends('layouts.app')

@section('content')
    <div class="container mx-auto">
        <h1 class="text-xl font-semibold mb-4">Inventario de Técnicos</h1>

        @if (session('success'))
            <div class="bg-emerald-100 text-emerald-800 px-4 py-2 rounded mb-4">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="bg-red-100 text-red-800 px-4 py-2 rounded mb-4">{{ session('error') }}</div>
        @endif

        {{-- Filtros --}}
        <form method="GET" class="grid grid-cols-12 gap-3 mb-4">
            @php
                // puedes adaptar esto según tus roles reales
                $isAdminOrAuditor = auth()->user()->hasRole('admin') || auth()->user()->hasRole('auditor');
            @endphp

            @if ($isAdminOrAuditor)
                <div class="col-span-12 md:col-span-3">
                    <label class="block text-sm font-medium mb-1">Técnico</label>
                    <select name="tecnico_id" class="w-full border rounded px-3 py-2">
                        <option value="">Todos</option>
                        @foreach ($tecnicos as $t)
                            <option value="{{ $t->id }}" @selected(request('tecnico_id') == $t->id)>{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>
            @else
                {{-- vista para técnico: muestra solo su inventario --}}
                <input type="hidden" name="tecnico_id" value="{{ auth()->id() }}">
                <div class="col-span-12 md:col-span-3">
                    <label class="block text-sm font-medium mb-1">Técnico</label>
                    <input type="text" value="{{ auth()->user()->name }}"
                        class="w-full border rounded px-3 py-2 bg-gray-100" readonly>
                </div>
            @endif
            <div class="col-span-12 md:col-span-4">
                <label class="block text-sm font-medium mb-1">Repuesto</label>
                <select name="repuesto_id" class="w-full border rounded px-3 py-2">
                    <option value="">Todos</option>
                    @foreach ($repuestos as $r)
                        @php $meta = $r->serie ?: ($r->modelo ?: ($r->marca ?: null)); @endphp
                        <option value="{{ $r->id }}" @selected(request('repuesto_id') == $r->id)>{{ $r->nombre }}@if ($meta)
                                ({{ $meta }})
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-span-12 md:col-span-3">
                <label class="block text-sm font-medium mb-1">Estado</label>
                <select name="estado" class="w-full border rounded px-3 py-2">
                    <option value="">Todos</option>
                    @foreach ($estados as $e)
                        <option value="{{ $e['v'] }}" @selected(request('estado') === $e['v'])>{{ $e['t'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-span-12 md:col-span-2 flex items-end">
                <button class="w-full bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded">Filtrar</button>
            </div>
        </form>

        {{-- Totales por técnico (resumen) --}}
        @if ($totalesPorTecnico->isNotEmpty())
            <div class="mb-4 text-sm text-gray-700">
                <strong>Totales por técnico:</strong>
                @foreach ($totalesPorTecnico as $tecId => $row)
                    <span class="inline-block mr-3">
                        {{ optional($tecnicos->firstWhere('id', $tecId))->name ?? 'Técnico ' . $tecId }}:
                        {{ $row->total }}
                    </span>
                @endforeach
            </div>
        @endif

        {{-- Tabla --}}
        <div class="overflow-x-auto">
            <table class="min-w-full border">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-3 py-2 border text-left">Técnico</th>
                        <th class="px-3 py-2 border text-left">Repuesto</th>
                        <th class="px-3 py-2 border text-right">Cantidad</th>
                        <th class="px-3 py-2 border text-left">Estado</th>
                        <th class="px-3 py-2 border text-left">Observación</th>
                        <th class="px-3 py-2 border text-left">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $it)
                        @php $meta = $it->repuesto->serie ?: ($it->repuesto->modelo ?: ($it->repuesto->marca ?: null)); @endphp
                        <tr>
                            <td class="px-3 py-2 border">{{ $it->tecnico->name ?? '—' }}</td>
                            <td class="px-3 py-2 border">{{ $it->repuesto->nombre }} @if ($meta)
                                    <span class="text-gray-500">({{ $meta }})</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 border text-right">{{ $it->cantidad }}</td>
                            <td class="px-3 py-2 border">
                                @if ($it->estado === 'asignado')
                                    <span class="px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-800">Asignado</span>
                                @else
                                    <span class="px-2 py-1 text-xs rounded bg-emerald-100 text-emerald-800">Devuelto</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 border text-sm">{{ $it->observacion }}</td>
                            <td class="px-3 py-2 border">
                                @if ($it->estado === 'asignado' && $it->cantidad > 0)
                                    <form action="{{ route('invtecnico.devolver', $it->id) }}" method="POST"
                                        class="flex items-center gap-2">
                                        @csrf
                                        <input type="number" name="cantidad" min="1" max="{{ $it->cantidad }}"
                                            value="1" class="border rounded px-2 py-1 w-24 text-right">
                                        <input type="text" name="observacion" placeholder="Obs. (opcional)"
                                            class="border rounded px-2 py-1 w-48">
                                        <button type="submit"
                                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded text-sm">
                                            Devolver a bodega
                                        </button>
                                    </form>
                                @else
                                    <span class="text-gray-500 text-sm">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3 py-6 text-center text-gray-500">Sin registros</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $items->links() }}
        </div>
    </div>
@endsection
