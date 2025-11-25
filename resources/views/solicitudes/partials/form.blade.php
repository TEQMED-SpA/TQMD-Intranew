<div class="container mx-auto">
    <h1 class="text-xl font-semibold mb-6">Nueva Solicitud</h1>

    @if (session('error'))
        <div class="bg-red-100 text-red-800 px-4 py-2 rounded mb-4">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('solicitudes.store') }}">
        @csrf

        <div class="grid grid-cols-12 gap-4 mb-6">
            <div class="col-span-12 md:col-span-3">
                <label class="block text-sm font-medium mb-1">Fecha solicitud</label>
                <input type="date" name="fecha_solicitud" value="{{ old('fecha_solicitud', now()->toDateString()) }}"
                    class="w-full border rounded-md px-3 py-2" required>
                @error('fecha_solicitud')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="col-span-12 md:col-span-3">
                <label class="block text-sm font-medium mb-1">Cliente</label>
                <select id="cliente_id" name="cliente_id" class="w-full border rounded-md px-3 py-2" required>
                    <option value="">-- Selecciona cliente --</option>
                    @foreach ($clientes as $c)
                        <option value="{{ $c->id }}" @selected(old('cliente_id') == $c->id)>{{ $c->nombre }}</option>
                    @endforeach
                </select>
                @error('cliente_id')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="col-span-12 md:col-span-3">
                <label class="block text-sm font-medium mb-1">Centro médico / Sucursal</label>
                <select id="clinica_id" name="clinica_id" class="w-full border rounded-md px-3 py-2" required>
                    <option value="">-- Selecciona centro --</option>
                </select>
                @error('clinica_id')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="col-span-12 md:col-span-3">
                <label class="block text-sm font-medium mb-1">Equipo / Máquina</label>
                <select id="equipo_id" name="equipo_id" class="w-full border rounded-md px-3 py-2" required>
                    <option value="">-- Selecciona equipo --</option>
                </select>
                @error('equipo_id')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium mb-2">Razón</label>
            <textarea name="razon" rows="3" class="w-full border rounded-md px-3 py-2" required>{{ old('razon') }}</textarea>
            @error('razon')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <h2 class="text-lg font-semibold mb-2">Repuestos solicitados</h2>

        @php
            $items = old('repuestos', $prefillRepuestos ?? []);
            if (empty($items)) {
                $items = [['repuesto_id' => null, 'cantidad' => 1]];
            }
        @endphp

        <div id="repuestosRepeater">
            @foreach ($items as $i => $item)
                <div class="grid grid-cols-12 gap-3 items-end mb-3 repeater-row">
                    <div class="col-span-8">
                        <label class="block text-sm font-medium mb-1">Repuesto</label>
                        <select name="repuestos[{{ $i }}][repuesto_id]"
                            class="w-full border rounded-md px-3 py-2" required>
                            <option value="">-- Selecciona repuesto --</option>
                            @foreach ($repuestos as $r)
                                @php $meta = $r->serie ?: ($r->modelo ?: ($r->marca ?: null)); @endphp
                                <option value="{{ $r->id }}" @selected((int) ($item['repuesto_id'] ?? 0) === $r->id)>
                                    {{ $r->nombre }}@if ($meta)
                                        ({{ $meta }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error("repuestos.$i.repuesto_id")
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="col-span-2">
                        <label class="block text-sm font-medium mb-1">Cantidad</label>
                        <input type="number" min="1" step="1"
                            name="repuestos[{{ $i }}][cantidad]"
                            value="{{ old("repuestos.$i.cantidad", $item['cantidad'] ?? 1) }}"
                            class="w-full border rounded-md px-3 py-2" required>
                        @error("repuestos.$i.cantidad")
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="col-span-2">
                        <button type="button"
                            class="removeRow bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-md">
                            Quitar
                        </button>
                    </div>

                    <div class="col-span-12">
                        <label class="block text-sm font-medium mb-1">Observación (opcional)</label>
                        <input type="text" name="repuestos[{{ $i }}][observacion]"
                            value="{{ old("repuestos.$i.observacion", $item['observacion'] ?? '') }}"
                            class="w-full border rounded-md px-3 py-2">
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mb-6">
            <button type="button" id="addRow" class="bg-gray-200 hover:bg-gray-300 px-3 py-2 rounded-md">
                Agregar repuesto
            </button>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-md">
                Guardar solicitud
            </button>
            <a href="{{ route('solicitudes.index') }}" class="px-4 py-2 rounded-md border">Cancelar</a>
        </div>
    </form>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const clienteSel = document.getElementById('cliente_id');
            const centroSel = document.getElementById('clinica_id');
            const equipoSel = document.getElementById('equipo_id');
            const addRowBtn = document.getElementById('addRow');
            const repeater = document.getElementById('repuestosRepeater');

            function resetSelect(sel, placeholder) {
                sel.innerHTML = '';
                const o = document.createElement('option');
                o.value = '';
                o.textContent = placeholder;
                sel.appendChild(o);
            }

            // Encadenado: Cliente -> Centros
            clienteSel.addEventListener('change', async () => {
                const id = clienteSel.value;
                resetSelect(centroSel, '-- Selecciona centro --');
                resetSelect(equipoSel, '-- Selecciona equipo --');
                if (!id) return;
                const res = await fetch(`{{ route('api.clientes.centros', ['cliente' => 'CID']) }}`
                    .replace('CID', id));
                (await res.json()).forEach(c => {
                    const o = document.createElement('option');
                    o.value = c.id;
                    o.textContent = c.nombre;
                    centroSel.appendChild(o);
                });
            });

            // Encadenado: Centro -> Equipos
            centroSel.addEventListener('change', async () => {
                const id = centroSel.value;
                resetSelect(equipoSel, '-- Selecciona equipo --');
                if (!id) return;
                const res = await fetch(`{{ route('api.centros.equipos', ['centro' => 'CID']) }}`
                    .replace('CID', id));
                (await res.json()).forEach(e => {
                    const o = document.createElement('option');
                    o.value = e.id;
                    o.textContent = `${e.codigo ?? 'SN'} — ${e.modelo ?? ''}`;
                    equipoSel.appendChild(o);
                });
            });

            // Repeater: Agregar fila
            addRowBtn.addEventListener('click', () => {
                const idx = repeater.querySelectorAll('.repeater-row').length;
                const html = `
        <div class="grid grid-cols-12 gap-3 items-end mb-3 repeater-row">
            <div class="col-span-8">
                <label class="block text-sm font-medium mb-1">Repuesto</label>
                <select name="repuestos[${idx}][repuesto_id]" class="w-full border rounded-md px-3 py-2" required>
                    <option value="">-- Selecciona repuesto --</option>
                    @foreach ($repuestos as $r)
                      @php $meta = $r->serie ?: ($r->modelo ?: ($r->marca ?: null)); @endphp
                      <option value="{{ $r->id }}">{{ $r->nombre }}@if ($meta) ({{ $meta }}) @endif</option>
                    @endforeach
                </select>
            </div>
            <div class="col-span-2">
                <label class="block text-sm font-medium mb-1">Cantidad</label>
                <input type="number" min="1" step="1" name="repuestos[${idx}][cantidad]" value="1" class="w-full border rounded-md px-3 py-2" required>
            </div>
            <div class="col-span-2">
                <button type="button" class="removeRow bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-md">Quitar</button>
            </div>
            <div class="col-span-12">
                <label class="block text-sm font-medium mb-1">Observación (opcional)</label>
                <input type="text" name="repuestos[${idx}][observacion]" class="w-full border rounded-md px-3 py-2">
            </div>
        </div>`;
                repeater.insertAdjacentHTML('beforeend', html);
            });

            // Repeater: Quitar fila
            repeater.addEventListener('click', (e) => {
                if (e.target.classList.contains('removeRow')) {
                    const row = e.target.closest('.repeater-row');
                    row?.remove();
                }
            });

            // Prefill de selects si hay old() (tras error de validación)
            const oldCliente = "{{ old('cliente_id') }}";
            const oldCentro = "{{ old('clinica_id') }}";
            const oldEquipo = "{{ old('equipo_id') }}";

            (async function restoreOldSelections() {
                if (!oldCliente) return;
                clienteSel.value = oldCliente;

                // Cargar centros del cliente y seleccionar old
                const resCentros = await fetch(`{{ route('api.clientes.centros', ['cliente' => 'CID']) }}`
                    .replace('CID', oldCliente));
                const centros = await resCentros.json();
                resetSelect(centroSel, '-- Selecciona centro --');
                centros.forEach(c => {
                    const o = document.createElement('option');
                    o.value = c.id;
                    o.textContent = c.nombre;
                    if (String(c.id) === String(oldCentro)) o.selected = true;
                    centroSel.appendChild(o);
                });

                if (!oldCentro) return;

                // Cargar equipos del centro y seleccionar old
                const resEquipos = await fetch(`{{ route('api.centros.equipos', ['centro' => 'CID']) }}`
                    .replace('CID', oldCentro));
                const equipos = await resEquipos.json();
                resetSelect(equipoSel, '-- Selecciona equipo --');
                equipos.forEach(e => {
                    const o = document.createElement('option');
                    o.value = e.id;
                    o.textContent = `${e.codigo ?? 'SN'} — ${e.modelo ?? ''}`;
                    if (String(e.id) === String(oldEquipo)) o.selected = true;
                    equipoSel.appendChild(o);
                });
            })();
        });
    </script>
@endpush
