<x-layouts.app :title="'Editar equipo: ' . $equipo->nombre">
    <div class="min-h-screen px-4 py-8 bg-zinc-50 dark:bg-zinc-900 transition-all">
        <div class="max-w-4xl mx-auto space-y-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-zinc-800 dark:text-white">Editar equipo</h1>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Actualiza la información asociada al
                        dispositivo.</p>
                </div>
                <a href="{{ route('equipos.show', $equipo) }}"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-zinc-200 dark:bg-zinc-800 text-zinc-800 dark:text-white hover:bg-zinc-300 dark:hover:bg-zinc-700">
                    <i class="fa fa-arrow-left text-xs"></i>
                    Volver
                </a>
            </div>

            @if (session('error'))
                <div class="p-3 rounded border border-red-200 bg-red-50 text-red-700 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            <div class="rounded-2xl bg-white dark:bg-zinc-900 shadow border border-zinc-200 dark:border-zinc-800 p-6">
                <form method="POST" action="{{ route('equipos.update', $equipo) }}" id="form-equipo-edit"
                    class="space-y-6">
                    @csrf
                    @method('PUT')

                    {{-- Cliente / Centro / Tipo equipo --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="cliente_id"
                                class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200 mb-1">Cliente</label>
                            <select id="cliente_id" name="cliente_id"
                                class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-sm text-zinc-900 dark:text-zinc-100 focus:ring focus:border-blue-500"
                                required>
                                <option value="">Selecciona un cliente</option>
                                @foreach ($clientes as $c)
                                    <option value="{{ $c->id }}" @selected(old('cliente_id', $equipo->centro?->cliente_id) == $c->id)>
                                        {{ $c->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('cliente_id')
                                <div class="text-xs text-red-500 mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label for="centro_medico_id"
                                class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200 mb-1">Centro
                                médico</label>
                            <select id="centro_medico_id" name="centro_medico_id"
                                class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-sm text-zinc-900 dark:text-zinc-100 focus:ring focus:border-blue-500"
                                required>
                                <option value="">Selecciona un centro</option>
                                @foreach ($centros as $cm)
                                    <option value="{{ $cm->id }}" @selected(old('centro_medico_id', $equipo->centro_medico_id) == $cm->id)>
                                        {{ $cm->centro_dialisis }}
                                    </option>
                                @endforeach
                            </select>
                            @error('centro_medico_id')
                                <div class="text-xs text-red-500 mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label for="tipo_equipo_id"
                                class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200 mb-1">Tipo de
                                equipo</label>
                            <select id="tipo_equipo_id" name="tipo_equipo_id"
                                class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-sm text-zinc-900 dark:text-zinc-100 focus:ring focus:border-blue-500">
                                <option value="">Selecciona una categoría</option>
                                @foreach ($tipos_equipo as $tipo)
                                    <option value="{{ $tipo->id }}" @selected(old('tipo_equipo_id', $equipo->tipo_equipo_id) == $tipo->id)>
                                        {{ $tipo->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tipo_equipo_id')
                                <div class="text-xs text-red-500 mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Datos generales --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="md:col-span-2">
                            <label for="nombre"
                                class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200 mb-1">Nombre del
                                equipo</label>
                            <input type="text" id="nombre" name="nombre" required
                                value="{{ old('nombre', $equipo->nombre) }}"
                                class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-sm text-zinc-900 dark:text-zinc-100 px-3 py-2 focus:ring focus:border-blue-500">
                            @error('nombre')
                                <div class="text-xs text-red-500 mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label for="estado"
                                class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200 mb-1">Estado</label>
                            <select name="estado" id="estado"
                                class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-sm text-zinc-900 dark:text-zinc-100 focus:ring focus:border-blue-500">
                                <option value="">—</option>
                                @foreach (['Operativo', 'En observacion', 'Fuera de servicio', 'Baja'] as $estado)
                                    <option value="{{ $estado }}" @selected(old('estado', $equipo->estado) === $estado)>
                                        {{ $estado }}
                                    </option>
                                @endforeach
                            </select>
                            @error('estado')
                                <div class="text-xs text-red-500 mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div id="dias-fuera-wrapper"
                            style="{{ old('estado', $equipo->estado) === 'Fuera de servicio' ? '' : 'display:none;' }}">
                            <label for="cant_dias_fuera_serv"
                                class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200 mb-1">Días fuera
                                de servicio</label>
                            <input type="number" min="0" max="365" name="cant_dias_fuera_serv"
                                id="cant_dias_fuera_serv"
                                value="{{ old('cant_dias_fuera_serv', $equipo->cant_dias_fuera_serv) }}"
                                class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-sm text-zinc-900 dark:text-zinc-100 px-3 py-2 focus:ring focus:border-blue-500">
                            @error('cant_dias_fuera_serv')
                                <div class="text-xs text-red-500 mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Características --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label
                                class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200 mb-1">Marca</label>
                            <input name="marca" value="{{ old('marca', $equipo->marca) }}"
                                class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-sm text-zinc-900 dark:text-zinc-100 px-3 py-2 focus:ring focus:border-blue-500">
                        </div>
                        <div>
                            <label
                                class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200 mb-1">Modelo</label>
                            <input name="modelo" value="{{ old('modelo', $equipo->modelo) }}"
                                class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-sm text-zinc-900 dark:text-zinc-100 px-3 py-2 focus:ring focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200 mb-1">Código /
                                SKU</label>
                            <input name="codigo" value="{{ old('codigo', $equipo->codigo) }}" required
                                class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-sm text-zinc-900 dark:text-zinc-100 px-3 py-2 focus:ring focus:border-blue-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200 mb-1">ID
                                máquina</label>
                            <input name="id_maquina" value="{{ old('id_maquina', $equipo->id_maquina) }}"
                                class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-sm text-zinc-900 dark:text-zinc-100 px-3 py-2 focus:ring focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200 mb-1">Número de
                                serie</label>
                            <input name="numero_serie" value="{{ old('numero_serie', $equipo->numero_serie) }}"
                                class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-sm text-zinc-900 dark:text-zinc-100 px-3 py-2 focus:ring focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200 mb-1">Horas de
                                uso</label>
                            <input type="number" min="0" name="horas_uso"
                                value="{{ old('horas_uso', $equipo->horas_uso) }}"
                                class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-sm text-zinc-900 dark:text-zinc-100 px-3 py-2 focus:ring focus:border-blue-500">
                        </div>
                    </div>

                    {{-- Mantenciones --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200 mb-1">Última
                                mantención</label>
                            <input type="date" name="ultima_mantencion"
                                value="{{ old('ultima_mantencion', $equipo->ultima_mantencion ? \Illuminate\Support\Carbon::parse($equipo->ultima_mantencion)->format('Y-m-d') : '') }}"
                                class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-sm text-zinc-900 dark:text-zinc-100 px-3 py-2 focus:ring focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200 mb-1">Próxima
                                mantención</label>
                            <input type="date" name="proxima_mantencion"
                                value="{{ old('proxima_mantencion', $equipo->proxima_mantencion ? \Illuminate\Support\Carbon::parse($equipo->proxima_mantencion)->format('Y-m-d') : '') }}"
                                class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-sm text-zinc-900 dark:text-zinc-100 px-3 py-2 focus:ring focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200 mb-1">Tipo de
                                mantención</label>
                            <select name="tipo_mantencion" id="tipo_mantencion"
                                class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-sm text-zinc-900 dark:text-zinc-100 focus:ring focus:border-blue-500">
                                <option value="">—</option>
                                @foreach (['T1', 'T2', 'T3', 'T4', 'Anual', 'Semestral', 'Trimestral', 'Casual', 'Otro'] as $tipo)
                                    <option value="{{ $tipo }}" @selected(old('tipo_mantencion', $equipo->tipo_mantencion) === $tipo)>
                                        {{ $tipo }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Descripción --}}
                    <div>
                        <label for="descripcion"
                            class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200 mb-1">Descripción /
                            comentarios</label>
                        <textarea id="descripcion" name="descripcion" rows="3"
                            class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-sm text-zinc-900 dark:text-zinc-100 px-3 py-2 focus:ring focus:border-blue-500">{{ old('descripcion', $equipo->descripcion) }}</textarea>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold">
                            <i class="fa fa-check text-xs"></i>
                            Actualizar equipo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const estadoSel = document.querySelector('#form-equipo-edit #estado');
                const diasWrapper = document.querySelector('#form-equipo-edit #dias-fuera-wrapper');
                const diasInput = document.querySelector('#form-equipo-edit #cant_dias_fuera_serv');
                const clienteSel = document.getElementById('cliente_id');
                const centroSel = document.getElementById('centro_medico_id');

                const toggleDias = () => {
                    if (!estadoSel || !diasWrapper || !diasInput) return;
                    const mostrar = estadoSel.value === 'Fuera de servicio';
                    diasWrapper.style.display = mostrar ? '' : 'none';
                    diasInput.required = mostrar;
                    diasInput.disabled = !mostrar;
                    if (!mostrar) {
                        diasInput.value = '';
                    }
                };
                estadoSel?.addEventListener('change', toggleDias);
                toggleDias();

                const resetCentros = (placeholder) => {
                    centroSel.innerHTML = '';
                    const opt = document.createElement('option');
                    opt.value = '';
                    opt.textContent = placeholder;
                    centroSel.appendChild(opt);
                };

                const cargarCentros = async (clienteId, prefilled = null) => {
                    resetCentros('Cargando centros…');
                    if (!clienteId) {
                        resetCentros('Selecciona un cliente primero');
                        return;
                    }
                    try {
                        const url = `{{ route('api.clientes.centros', ['cliente' => 'CID']) }}`.replace('CID',
                            clienteId);
                        const res = await fetch(url, {
                            headers: {
                                'Accept': 'application/json'
                            }
                        });
                        if (!res.ok) throw new Error('Error al cargar centros');
                        const data = await res.json();
                        resetCentros('Selecciona un centro');
                        data.forEach(c => {
                            const opt = document.createElement('option');
                            opt.value = c.id;
                            opt.textContent = c.nombre;
                            centroSel.appendChild(opt);
                        });
                        if (prefilled) {
                            centroSel.value = prefilled;
                        }
                    } catch (error) {
                        console.error(error);
                        resetCentros('No se pudo cargar centros');
                    }
                };

                clienteSel?.addEventListener('change', () => cargarCentros(clienteSel.value));

                // Rehidratación si venimos de validación fallida
                const oldCliente = "{{ old('cliente_id', $equipo->centro?->cliente_id) }}";
                const oldCentro = "{{ old('centro_medico_id', $equipo->centro_medico_id) }}";
                if (oldCliente) {
                    cargarCentros(oldCliente, oldCentro);
                }
            });
        </script>
    @endpush
</x-layouts.app>
