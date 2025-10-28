<h2 class="text-lg font-semibold mb-2">Ítems solicitados</h2>
<div class="overflow-x-auto">
    <table class="min-w-full border">
        <thead>
            <tr class="bg-gray-50">
                <th class="px-3 py-2 text-left border">Repuesto</th>
                <th class="px-3 py-2 text-right border">Solicitado</th>
                <th class="px-3 py-2 text-right border">Entregado</th>
                <th class="px-3 py-2 text-right border">Restante</th>
                <th class="px-3 py-2 text-right border">Stock</th>
                <th class="px-3 py-2 text-left border">Uso</th>
                <th class="px-3 py-2 text-left border">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($solicitud->repuestos as $rep)
                @php
                    $solicitado = (int) $rep->pivot->cantidad;
                    $entregado = (int) \Illuminate\Support\Facades\DB::table('salidas')
                        ->where('solicitud_id', $solicitud->id)
                        ->where('repuesto_id', $rep->id)
                        ->sum('cantidad');
                    $restante = max(0, $solicitado - $entregado);
                    $stock = (int) ($rep->stock ?? 0);
                @endphp
                <tr>
                    <td class="px-3 py-2 border">
                        {{ $rep->nombre }}
                        @php $meta = $rep->serie ?: ($rep->modelo ?: ($rep->marca ?: null)); @endphp
                        @if ($meta)
                            <span class="text-gray-500 text-sm">({{ $meta }})</span>
                        @endif
                    </td>
                    <td class="px-3 py-2 border text-right">{{ $solicitado }}</td>
                    <td class="px-3 py-2 border text-right">{{ $entregado }}</td>
                    <td class="px-3 py-2 border text-right">{{ $restante }}</td>
                    <td class="px-3 py-2 border text-right">{{ $stock }}</td>
                    <td class="px-3 py-2 border">
                        @if (is_null($rep->pivot->usado))
                            <span class="px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-800">Pendiente</span>
                        @elseif($rep->pivot->usado)
                            <span class="px-2 py-1 text-xs rounded bg-emerald-100 text-emerald-800">Usado</span>
                        @else
                            <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-800">
                                No usado ({{ $rep->pivot->destino_devolucion ?? '—' }})
                            </span>
                        @endif
                    </td>
                    <td class="px-3 py-2 border">
                        <div class="flex flex-wrap gap-2 items-center">
                            {{-- Form: Entregar (aprueba y descuenta stock) --}}
                            @if ($restante > 0 && $stock > 0)
                                <form action="{{ route('solicitudes.repuestos.entregar', [$solicitud, $rep->id]) }}"
                                    method="POST" class="flex items-center gap-2">
                                    @csrf
                                    <input type="number" name="cantidad"
                                        class="border rounded px-2 py-1 w-24 text-right" min="1" step="1"
                                        max="{{ min($restante, $stock) }}" value="{{ min(1, $restante, $stock) }}">
                                    <button type="submit"
                                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded text-sm">
                                        Entregar
                                    </button>
                                </form>
                            @else
                                <span class="text-sm text-gray-500">—</span>
                            @endif

                            {{-- Form: Marcar uso/no uso (de Fase A, se mantiene) --}}
                            <form action="{{ route('solicitudes.repuestos.marcarUso', [$solicitud, $rep->id]) }}"
                                method="POST" class="flex items-center gap-2">
                                @csrf
                                @method('PUT')
                                <select name="usado" class="border rounded px-2 py-1 text-sm">
                                    <option value="1" @selected($rep->pivot->usado === 1)>Usado</option>
                                    <option value="0" @selected($rep->pivot->usado === 0)>No usado</option>
                                    <option value="" @selected(is_null($rep->pivot->usado))>Pendiente</option>
                                </select>
                                <select name="destino_devolucion" class="border rounded px-2 py-1 text-sm"
                                    title="Destino si NO se usó">
                                    <option value="">— destino —</option>
                                    <option value="bodega" @selected($rep->pivot->destino_devolucion === 'bodega')>Bodega</option>
                                    <option value="laboratorio" @selected($rep->pivot->destino_devolucion === 'laboratorio')>Laboratorio</option>
                                    <option value="cliente" @selected($rep->pivot->destino_devolucion === 'cliente')>Cliente</option>
                                    <option value="tecnico" @selected($rep->pivot->destino_devolucion === 'tecnico')>Técnico</option>
                                </select>
                                <button type="submit"
                                    class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-1 rounded text-sm">
                                    Guardar uso
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
