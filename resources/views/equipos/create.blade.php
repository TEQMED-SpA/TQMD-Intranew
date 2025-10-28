<x-layouts.app :title="$title ?? 'Nuevo Equipo'">
    <div class="min-h-screen px-4 py-8 bg-zinc-50 dark:bg-zinc-800 transition-all">
        <div class="max-w-4xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-zinc-800 dark:text-white">Nuevo Equipo</h1>
                <a href="{{ route('equipos.index') }}">
                    <button type="button"
                        class="bg-zinc-200 dark:bg-zinc-700 text-zinc-100 dark:text-zinc-100 font-semibold px-4 py-2 rounded-lg hover:bg-zinc-300 dark:hover:bg-zinc-600 transition flex items-center gap-2">
                        <i class="fa fa-arrow-left"></i>
                        Volver
                    </button>
                </a>
            </div>

            <div class="rounded-lg shadow bg-white dark:bg-zinc-900 p-6">
                @if (session('error'))
                    <div class="mb-4 text-red-500 text-sm">{{ session('error') }}</div>
                @endif

                <form method="POST" action="{{ route('equipos.store') }}" class="grid gap-4">
                    @csrf

                    {{-- Cliente y Centro (dependientes) --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="cliente_id"
                                class="block text-zinc-700 dark:text-zinc-200 font-semibold mb-1">Cliente</label>
                            <select id="cliente_id" name="cliente_id"
                                class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring focus:border-blue-400"
                                required>
                                <option value="">Seleccione un cliente</option>
                                @foreach ($clientes as $c)
                                    <option value="{{ $c->id }}" @selected(old('cliente_id') == $c->id)>{{ $c->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('cliente_id')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label for="centro_medico_id"
                                class="block text-zinc-700 dark:text-zinc-200 font-semibold mb-1">Centro médico /
                                Sucursal</label>
                            <select id="centro_medico_id" name="centro_medico_id"
                                class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring focus:border-blue-400"
                                required>
                                <option value="">Seleccione un cliente primero</option>
                            </select>
                            @error('centro_medico_id')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Nombre y Estado --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="nombre"
                                class="block text-zinc-700 dark:text-zinc-200 font-semibold mb-1">Nombre</label>
                            <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}"
                                class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring focus:border-blue-400"
                                required>
                            @error('nombre')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label for="estado"
                                class="block text-zinc-700 dark:text-zinc-200 font-semibold mb-1">Estado</label>
                            <select name="estado" id="estado"
                                class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring focus:border-blue-400">
                                <option value="">—</option>
                                <option value="operativo" @selected(old('estado') === 'operativo')>Operativo</option>
                                <option value="mantenimiento" @selected(old('estado') === 'mantenimiento')>Mantenimiento</option>
                                <option value="baja" @selected(old('estado') === 'baja')>Baja</option>
                            </select>
                            @error('estado')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Marca / Modelo / SKU --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="marca"
                                class="block text-zinc-700 dark:text-zinc-200 font-semibold mb-1">Marca</label>
                            <input name="marca" id="marca" value="{{ old('marca') }}"
                                class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring focus:border-blue-400">
                            @error('marca')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label for="modelo"
                                class="block text-zinc-700 dark:text-zinc-200 font-semibold mb-1">Modelo</label>
                            <input name="modelo" id="modelo" value="{{ old('modelo') }}"
                                class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring focus:border-blue-400">
                            @error('modelo')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label for="sku" class="block text-zinc-700 dark:text-zinc-200 font-semibold mb-1">SKU
                                (interno)</label>
                            <input name="sku" id="sku" value="{{ old('sku') }}"
                                class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring focus:border-blue-400">
                            @error('sku')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- ID máquina / Nº serie / Horas uso --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="id_maquina" class="block text-zinc-700 dark:text-zinc-200 font-semibold mb-1">ID
                                máquina (centro)</label>
                            <input name="id_maquina" id="id_maquina" value="{{ old('id_maquina') }}"
                                class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring focus:border-blue-400">
                            @error('id_maquina')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label for="numero_serie"
                                class="block text-zinc-700 dark:text-zinc-200 font-semibold mb-1">Nº serie</label>
                            <input name="numero_serie" id="numero_serie" value="{{ old('numero_serie') }}"
                                class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring focus:border-blue-400">
                            @error('numero_serie')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label for="horas_uso"
                                class="block text-zinc-700 dark:text-zinc-200 font-semibold mb-1">Horas de uso</label>
                            <input type="number" min="0" name="horas_uso" id="horas_uso"
                                value="{{ old('horas_uso', 0) }}"
                                class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring focus:border-blue-400">
                            @error('horas_uso')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Mantenciones --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="ultima_mantencion"
                                class="block text-zinc-700 dark:text-zinc-200 font-semibold mb-1">Últ.
                                mantención</label>
                            <input type="date" name="ultima_mantencion" id="ultima_mantencion"
                                value="{{ old('ultima_mantencion') }}"
                                class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring focus:border-blue-400">
                            @error('ultima_mantencion')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label for="proxima_mantencion"
                                class="block text-zinc-700 dark:text-zinc-200 font-semibold mb-1">Próx.
                                mantención</label>
                            <input type="date" name="proxima_mantencion" id="proxima_mantencion"
                                value="{{ old('proxima_mantencion') }}"
                                class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring focus:border-blue-400">
                            @error('proxima_mantencion')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label for="tipo_mantencion"
                                class="block text-zinc-700 dark:text-zinc-200 font-semibold mb-1">Tipo
                                mantención</label>
                            <select name="tipo_mantencion" id="tipo_mantencion"
                                class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring focus:border-blue-400">
                                <option value="">—</option>
                                @foreach (['T1', 'T2', 'T3', 'T4'] as $t)
                                    <option value="{{ $t }}" @selected(old('tipo_mantencion') === $t)>
                                        {{ $t }}</option>
                                @endforeach
                            </select>
                            @error('tipo_mantencion')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Imagen --}}
                    <div>
                        <label for="imagen" class="block text-zinc-700 dark:text-zinc-200 font-semibold mb-1">Imagen
                            (URL)</label>
                        <input name="imagen" id="imagen" value="{{ old('imagen') }}"
                            class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring focus:border-blue-400"
                            placeholder="https://...">
                        @error('imagen')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Descripción --}}
                    <div>
                        <label for="descripcion"
                            class="block text-zinc-700 dark:text-zinc-200 font-semibold mb-1">Descripción</label>
                        <textarea name="descripcion" id="descripcion" rows="3"
                            class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring focus:border-blue-400">{{ old('descripcion') }}</textarea>
                        @error('descripcion')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Botones --}}
                    <div class="flex justify-end gap-2">
                        <button type="submit"
                            class="bg-blue-500 text-white font-semibold px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                            <i class="fa fa-check"></i>
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            (function() {
                const clienteSel = document.getElementById('cliente_id');
                const centroSel = document.getElementById('centro_medico_id');

                function resetSelect(sel, ph) {
                    sel.innerHTML = '';
                    const o = document.createElement('option');
                    o.value = '';
                    o.textContent = ph;
                    sel.appendChild(o);
                }

                // Cargar centros según cliente
                clienteSel?.addEventListener('change', async () => {
                    resetSelect(centroSel, '-- Selecciona centro --');
                    if (!clienteSel.value) return;

                    try {
                        const url = `{{ route('api.clientes.centros', ['cliente' => 'CID']) }}`.replace('CID',
                            clienteSel.value);
                        const res = await fetch(url);
                        const data = await res.json();

                        data.forEach(c => {
                            const o = document.createElement('option');
                            o.value = c.id;
                            o.textContent = c.nombre;
                            centroSel.appendChild(o);
                        });
                    } catch (e) {
                        console.error(e);
                    }
                });

                // Si hubo old('cliente_id'), dispara el change para repoblar centros
                @if (old('cliente_id'))
                    document.addEventListener('DOMContentLoaded', () => {
                        const evt = new Event('change');
                        clienteSel.dispatchEvent(evt);
                        // Luego selecciona old('centro_medico_id') si existe
                        const oldCentro = "{{ old('centro_medico_id') }}";
                        if (oldCentro) {
                            // pequeño delay para esperar el fetch
                            setTimeout(() => {
                                centroSel.value = oldCentro;
                            }, 300);
                        }
                    });
                @endif
            })();
        </script>
    @endpush
</x-layouts.app>
