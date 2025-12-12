@php
    $tipoInformePreventivo = $tipoInformePreventivo ?? null;
    $clientes = $clientes ?? collect();
    $centrosMedicos = $centrosMedicos ?? collect();
    $equipos = $equipos ?? collect();
    $repuestos = $repuestos ?? collect();
    $siguienteNumero = $siguienteNumero ?? null;
    $configKey = $configKey ?? 'fresenius';

    $tipoNombre = $tipoInformePreventivo->nombre ?? 'Informe Preventivo';
    $tipoId = old('tipo_informe_preventivo_id', $tipoInformePreventivo->id ?? null);
    $oldRepuestos = old('repuestos', [['repuesto_id' => null, 'cantidad' => null]]);
    $inspeccionesConfig = config('preventivos.' . $configKey, config('preventivos.fresenius'));
    $seccionesInspeccion = $inspeccionesConfig['sections'] ?? [];
    $defaultOptions = ['SI', 'NO', 'N/A'];
    $inspeccionIndex = 0;
@endphp

<form id="form-preventivo" method="POST" action="{{ route('informes.preventivos.store') }}"
    data-tipo-equipo-id="{{ $tipoInformePreventivo->tipo_equipo_id ?? '' }}" class="max-w-5xl mx-auto">
    @csrf
    <input type="hidden" name="tipo_informe_preventivo_id" value="{{ $tipoId }}">

    {{-- Errores generales --}}
    @if ($errors->any())
        <div
            class="mb-4 p-3 rounded-lg bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-200 text-sm border border-red-200 dark:border-red-700">
            <div class="flex items-center gap-2 mb-1">
                <i class="fa fa-triangle-exclamation"></i>
                <b>Corrige los siguientes campos del informe preventivo:</b>
            </div>
            <ul class="list-disc ml-5 space-y-0.5">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div
        class="bg-white dark:bg-zinc-800 rounded-xl shadow-md border border-zinc-200 dark:border-zinc-700 p-6 md:p-7 space-y-6">

        {{-- Encabezado --}}
        <div
            class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 border-b border-zinc-100 dark:border-zinc-700 pb-3">
            <div>
                <h3 class="text-xl font-bold text-zinc-900 dark:text-zinc-50 flex items-center gap-2">
                    <i class="fa fa-clipboard-check text-emerald-500"></i>
                    {{ $tipoNombre }}
                </h3>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                    Registra la información de la mantención preventiva para este tipo de informe.
                </p>
            </div>

            <div class="flex flex-col items-end gap-1">
                <span
                    class="text-[11px] uppercase tracking-wide px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-200 inline-flex items-center gap-1">
                    <i class="fa fa-shield-heart text-[10px]"></i>
                    Preventivo
                </span>
                <span class="text-xs text-zinc-500 dark:text-zinc-400">
                    N° Reporte de Servicio:
                    <span class="font-semibold text-zinc-800 dark:text-zinc-100">
                        {{ $siguienteNumero ?? '—' }}
                    </span>
                </span>
            </div>
        </div>

        {{-- Datos generales --}}
        <div class="space-y-4">
            <h4 class="text-sm font-semibold text-zinc-700 dark:text-zinc-200 flex items-center gap-2">
                <span class="w-1 h-4 rounded-full bg-emerald-500"></span>
                Datos generales
            </h4>

            {{-- Fecha + Cliente --}}
            <div class="grid md:grid-cols-2 gap-4">
                {{-- Fecha --}}
                <div>
                    <label for="fecha"
                        class="block text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase mb-1.5">
                        Fecha de mantención
                    </label>
                    <input id="fecha" type="date" name="fecha" value="{{ old('fecha') }}"
                        class="w-full px-3 py-2.5 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50
                               dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 text-sm focus:outline-none
                               focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                        required>
                    @error('fecha')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Cliente --}}
                <div>
                    <label for="cliente_id"
                        class="block text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase mb-1.5">
                        Cliente
                    </label>
                    <select id="cliente_id" name="cliente_id"
                        class="w-full px-3 py-2.5 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50
                               dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 text-sm focus:outline-none
                               focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                        required>
                        <option value="">Selecciona un cliente…</option>
                        @foreach ($clientes as $cliente)
                            <option value="{{ $cliente->id }}" @selected(old('cliente_id') == $cliente->id)>
                                {{ $cliente->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('cliente_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Centro / Equipo --}}
            <div class="grid md:grid-cols-2 gap-4 mt-4">
                {{-- Centro --}}
                <div>
                    <label for="centro_medico_id"
                        class="block text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase mb-1.5">
                        Centro / Sucursal
                    </label>
                    <select id="centro_medico_id" name="centro_medico_id"
                        class="w-full px-3 py-2.5 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50
                               dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 text-sm focus:outline-none
                               focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                        required>
                        <option value="">Selecciona un centro…</option>
                        @foreach ($centrosMedicos as $centro)
                            <option value="{{ $centro->id }}" @selected(old('centro_medico_id') == $centro->id)>
                                {{ $centro->centro_dialisis }}
                            </option>
                        @endforeach
                    </select>
                    @error('centro_medico_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Equipo --}}
                <div>
                    <label for="equipo_id"
                        class="block text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase mb-1.5">
                        Equipo
                    </label>
                    <select id="equipo_id" name="equipo_id"
                        class="w-full px-3 py-2.5 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50
                               dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 text-sm focus:outline-none
                               focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                        {{ old('centro_medico_id') ? '' : 'disabled' }} required>
                        <option value="">Selecciona un equipo…</option>
                        @foreach ($equipos as $equipo)
                            <option value="{{ $equipo->id }}" data-numero-serie="{{ $equipo->numero_serie }}"
                                data-horas-uso="{{ $equipo->horas_uso }}" @selected(old('equipo_id') == $equipo->id)>
                                ({{ $equipo->codigo }})
                                - {{ $equipo->numero_serie }}
                            </option>
                        @endforeach
                    </select>
                    @error('equipo_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            @php
                $horasOperacionValue = old('horas_operacion');
                if ($horasOperacionValue === null && old('equipo_id')) {
                    $equipoSeleccionado = $equipos->firstWhere('id', old('equipo_id'));
                    if ($equipoSeleccionado && !is_null($equipoSeleccionado->horas_uso)) {
                        $horasOperacionValue = (int) $equipoSeleccionado->horas_uso;
                    }
                }
            @endphp

            {{-- Tipo trabajo / Condición / Serie / Horas --}}
            <div class="grid md:grid-cols-2 gap-4 mt-4">
                <div>
                    <label for="tipo_trabajo"
                        class="block text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase mb-1.5">
                        Tipo de trabajo
                    </label>
                    <select id="tipo_trabajo" name="tipo_trabajo"
                        class="w-full px-3 py-2.5 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50
                               dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 text-sm focus:outline-none
                               focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                        required>
                        <option value="">Selecciona una opción…</option>
                        @foreach (['T1', 'T2', 'T3', 'T4', 'Anual', 'Semestral', 'Trimestral', 'Ocasional', 'Otro'] as $tipo)
                            <option value="{{ $tipo }}" @selected(old('tipo_trabajo') === $tipo)>{{ $tipo }}
                            </option>
                        @endforeach
                    </select>
                    @error('tipo_trabajo')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="condicion_equipo"
                        class="block text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase mb-1.5">
                        Condición del equipo
                    </label>
                    <select id="condicion_equipo" name="condicion_equipo"
                        class="w-full px-3 py-2.5 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50
                               dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 text-sm focus:outline-none
                               focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                        required>
                        <option value="">Selecciona una opción…</option>
                        @foreach (['Operativo', 'En observacion', 'Fuera de servicio', 'Baja'] as $estado)
                            <option value="{{ $estado }}" @selected(old('condicion_equipo') === $estado)>{{ $estado }}
                            </option>
                        @endforeach
                    </select>
                    @error('condicion_equipo')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="numero_serie"
                        class="block text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase mb-1.5">
                        N° de Serie
                    </label>
                    <input id="numero_serie" type="text" name="numero_serie" value="{{ old('numero_serie') }}"
                        class="w-full px-3 py-2.5 border border-zinc-300 dark:border-zinc-700 rounded-lg
                               bg-zinc-50 dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 text-sm
                               focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                        required>
                    @error('numero_serie')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="horas_operacion"
                        class="block text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase mb-1.5">
                        Horas de operación
                    </label>
                    <input id="horas_operacion" type="number" min="0" name="horas_operacion"
                        value="{{ $horasOperacionValue }}"
                        class="w-full px-3 py-2.5 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50
                               dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 text-sm focus:outline-none
                               focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                        required>
                    <p class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-1">
                        Debe ser ≥ a las horas actuales del equipo.
                    </p>
                    @error('horas_operacion')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Inspecciones --}}
        <div class="space-y-5">
            @foreach ($seccionesInspeccion as $section)
                <div class="space-y-2">
                    <h4 class="text-sm font-semibold text-zinc-700 dark:text-zinc-200 flex items-center gap-2">
                        <span class="w-1 h-4 rounded-full bg-sky-500"></span>
                        {{ $section['title'] ?? 'Inspecciones' }}
                    </h4>
                    <div class="overflow-x-auto rounded-lg border border-zinc-200 dark:border-zinc-700">
                        <table class="min-w-full text-xs md:text-sm">
                            <thead class="bg-zinc-100 dark:bg-zinc-800">
                                <tr>
                                    <th
                                        class="px-3 py-2 text-left font-semibold text-zinc-700 dark:text-zinc-100 w-10">
                                        #</th>
                                    <th class="px-3 py-2 text-left font-semibold text-zinc-700 dark:text-zinc-100">
                                        Descripción</th>
                                    <th
                                        class="px-3 py-2 text-center font-semibold text-zinc-700 dark:text-zinc-100 w-48">
                                        Respuesta</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($section['items'] ?? [] as $item)
                                    @php
                                        $options = $item['options'] ?? $defaultOptions;
                                        $type = $item['type'] ?? 'options';
                                        $oldResp = old("inspecciones.$inspeccionIndex");
                                        $oldText = old("inspecciones_text.$inspeccionIndex");
                                        $oldComment = old("inspecciones_comment.$inspeccionIndex");
                                        $requiresComment = (bool) ($item['requires_comment'] ?? false);
                                        $commentPlaceholder = $item['comment_placeholder'] ?? 'Valor registrado';
                                        $commentFields = $item['comment_fields'] ?? null;
                                        $oldGroupValues = old("inspecciones_comment_group.$inspeccionIndex", []);
                                    @endphp
                                    <tr class="border-t border-zinc-200 dark:border-zinc-700"
                                        @if ($requiresComment && $type !== 'text') x-data="{
                                                selected: @js($oldResp ?? ''),
                                                clearFields() {
                                                    if (this.selected !== 'SI') {
                                                        @if ($commentFields)
                                                            @foreach ($commentFields as $field)
                                                                @php
                                                                    $fieldKey = $field['key'] ?? 'valor';
                                                                @endphp
                                                                if (this.$refs['field{{ $inspeccionIndex }}{{ $fieldKey }}']) {
                                                                    this.$refs['field{{ $inspeccionIndex }}{{ $fieldKey }}'].value = '';
                                                                }
                                                            @endforeach
                                                        @else
                                                            if (this.$refs.comment{{ $inspeccionIndex }}) {
                                                                this.$refs.comment{{ $inspeccionIndex }}.value = '';
                                                            } @endif
                                        } } }" x-effect="clearFields()" @endif>
                                        <td class="px-3 py-2 align-top text-zinc-700 dark:text-zinc-200">
                                            {{ $inspeccionIndex + 1 }}
                                        </td>
                                        <td class="px-3 py-2 align-top text-zinc-900 dark:text-zinc-100">
                                            {{ $item['label'] ?? '—' }}
                                        </td>
                                        <td class="px-3 py-2 align-top">
                                            @if ($type === 'text')
                                                <input type="text"
                                                    name="inspecciones_text[{{ $inspeccionIndex }}]"
                                                    value="{{ $oldText }}"
                                                    placeholder="{{ $item['placeholder'] ?? 'Escribe aquí' }}"
                                                    class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-950 text-xs md:text-sm text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-sky-500">
                                                @error('inspecciones_text.' . $inspeccionIndex)
                                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                                @enderror
                                            @else
                                                <div class="flex flex-wrap gap-2">
                                                    @foreach ($options as $option)
                                                        @php
                                                            $optionValue = $option;
                                                        @endphp
                                                        <label
                                                            class="inline-flex items-center gap-1 text-[11px] md:text-xs">
                                                            <input type="radio"
                                                                name="inspecciones[{{ $inspeccionIndex }}]"
                                                                value="{{ $optionValue }}"
                                                                @if ($requiresComment) x-model="selected" @endif
                                                                class="text-emerald-600 focus:ring-emerald-500"
                                                                {{ $oldResp === $optionValue ? 'checked' : '' }}
                                                                {{ $loop->first ? 'required' : '' }}>
                                                            <span>{{ $optionValue }}</span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                                @error('inspecciones.' . $inspeccionIndex)
                                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                                @enderror

                                                @if ($requiresComment)
                                                    <div class="mt-3 space-y-3" x-cloak x-show="selected === 'SI'">
                                                        @if ($commentFields)
                                                            <div class="grid gap-3 md:grid-cols-2">
                                                                @foreach ($commentFields as $field)
                                                                    @php
                                                                        $fieldKey = $field['key'] ?? 'valor';
                                                                        $fieldLabel =
                                                                            $field['label'] ??
                                                                            ucwords(str_replace('_', ' ', $fieldKey));
                                                                        $fieldPlaceholder =
                                                                            $field['placeholder'] ??
                                                                            $commentPlaceholder;
                                                                        $fieldValue = $oldGroupValues[$fieldKey] ?? '';
                                                                    @endphp
                                                                    <div class="space-y-1">
                                                                        <label
                                                                            class="text-[11px] font-semibold text-zinc-600 dark:text-zinc-300">
                                                                            {{ $fieldLabel }}
                                                                        </label>
                                                                        <input type="text"
                                                                            name="inspecciones_comment_group[{{ $inspeccionIndex }}][{{ $fieldKey }}]"
                                                                            value="{{ $fieldValue }}"
                                                                            placeholder="{{ $fieldPlaceholder }}"
                                                                            class="w-full px-3 py-2 border border-sky-300 dark:border-sky-600 rounded-lg bg-white dark:bg-zinc-950 text-xs md:text-sm text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-sky-500"
                                                                            x-ref="field{{ $inspeccionIndex }}{{ $fieldKey }}">
                                                                        @error('inspecciones_comment_group.' .
                                                                            $inspeccionIndex . '.' . $fieldKey)
                                                                            <p class="text-red-500 text-xs mt-1">
                                                                                {{ $message }}</p>
                                                                        @enderror
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                            <p class="text-[11px] text-zinc-500 dark:text-zinc-400">
                                                                Registra todos los valores solicitados cuando la
                                                                respuesta sea “SI”.
                                                            </p>
                                                        @else
                                                            <input type="text"
                                                                name="inspecciones_comment[{{ $inspeccionIndex }}]"
                                                                value="{{ $oldComment }}"
                                                                placeholder="{{ $commentPlaceholder }}"
                                                                class="w-full px-3 py-2 border border-sky-300 dark:border-sky-600 rounded-lg bg-white dark:bg-zinc-950 text-xs md:text-sm text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-sky-500"
                                                                x-ref="comment{{ $inspeccionIndex }}">
                                                            <p
                                                                class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-1">
                                                                Registra el valor medido cuando la respuesta sea “SI”.
                                                            </p>
                                                            @error('inspecciones_comment.' . $inspeccionIndex)
                                                                <p class="text-red-500 text-xs mt-1">{{ $message }}
                                                                </p>
                                                            @enderror
                                                        @endif
                                                    </div>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                    @php $inspeccionIndex++; @endphp
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach

            @error('inspecciones')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Repuestos --}}
        <div class="space-y-3"
            x-data='{
                repuestos: @json($oldRepuestos),
                agregar() { this.repuestos.push({ repuesto_id: null, cantidad: null }); },
                quitar(index) { if (this.repuestos.length > 1) { this.repuestos.splice(index, 1); } }
            }'>
            <h4 class="text-sm font-semibold text-zinc-700 dark:text-zinc-200 flex items-center gap-2">
                <span class="w-1 h-4 rounded-full bg-amber-500"></span>
                Repuestos utilizados
            </h4>

            <div class="space-y-3">
                <template x-for="(item, index) in repuestos" :key="index">
                    <div class="grid md:grid-cols-2 gap-4 border border-zinc-200 dark:border-zinc-700 rounded-lg p-4">
                        <div>
                            <label
                                class="block text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase mb-1.5">
                                Repuesto
                            </label>
                            <select
                                class="w-full px-3 py-2.5 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                :name="`repuestos[${index}][repuesto_id]`" x-model="item.repuesto_id">
                                <option value="">Selecciona un repuesto…</option>
                                @foreach ($repuestos as $repuesto)
                                    <option value="{{ $repuesto->id }}">{{ $repuesto->nombre }} (Stock:
                                        {{ $repuesto->stock }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label
                                class="block text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase mb-1.5">
                                Cantidad
                            </label>
                            <div class="flex items-center gap-2">
                                <input type="number" min="1"
                                    class="w-full px-3 py-2.5 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                    :name="`repuestos[${index}][cantidad]`" x-model="item.cantidad">
                                <button type="button" class="text-red-500 hover:text-red-600"
                                    @click="quitar(index)">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>

                <button type="button" @click="agregar()"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-600 rounded-lg hover:bg-amber-50 dark:hover:bg-amber-500/10 transition">
                    <i class="fa fa-plus text-xs"></i>
                    Añadir repuesto
                </button>
            </div>

            @error('repuestos.*.repuesto_id')
                <p class="text-red-500 text-xs">{{ $message }}</p>
            @enderror
            @error('repuestos.*.cantidad')
                <p class="text-red-500 text-xs">{{ $message }}</p>
            @enderror
        </div>

        {{-- Comentarios y próximo control --}}
        <div class="grid md:grid-cols-2 gap-6">
            <div class="space-y-2">
                <h4 class="text-sm font-semibold text-zinc-700 dark:text-zinc-200 flex items-center gap-2">
                    <span class="w-1 h-4 rounded-full bg-indigo-500"></span>
                    Comentarios
                </h4>
                <textarea name="comentarios" rows="4"
                    class="w-full px-3 py-2.5 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50
                           dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 text-sm focus:outline-none
                           focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Observaciones adicionales, hallazgos, recomendaciones, etc. (opcional)">{{ old('comentarios') }}</textarea>
                @error('comentarios')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-2">
                <h4 class="text-sm font-semibold text-zinc-700 dark:text-zinc-200 flex items-center gap-2">
                    <span class="w-1 h-4 rounded-full bg-pink-500"></span>
                    Próximo control
                </h4>
                <div>
                    <label for="fecha_proximo_control"
                        class="block text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase mb-1.5">
                        Fecha próxima mantención (opcional)
                    </label>
                    <input id="fecha_proximo_control" type="date" name="fecha_proximo_control"
                        value="{{ old('fecha_proximo_control') }}"
                        class="w-full px-3 py-2.5 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50
                               dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 text-sm focus:outline-none
                               focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                    @error('fecha_proximo_control')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Firmas --}}
        <div class="space-y-4">
            <h4 class="text-sm font-semibold text-zinc-700 dark:text-zinc-200 flex items-center gap-2">
                <span class="w-1 h-4 rounded-full bg-purple-500"></span>
                Firmas
            </h4>

            <div class="grid gap-6 md:grid-cols-2">
                {{-- Firma Técnico --}}
                <div class="space-y-2">
                    <p class="text-xs text-zinc-600 dark:text-zinc-300">
                        La firma del técnico es obligatoria. Dibuja con el mouse o lápiz.
                    </p>
                    <div
                        class="border border-dashed border-zinc-300 dark:border-zinc-600 rounded-lg p-4 bg-zinc-50 dark:bg-zinc-900">
                        <canvas id="signature-pad-preventivo-tecnico"
                            class="border border-zinc-200 dark:border-zinc-700 rounded bg-white"
                            style="height: 180px; width: 100%;"></canvas>
                        <div class="mt-3 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                            <button type="button" id="clear-signature-preventivo-tecnico"
                                class="inline-flex items-center px-3 py-1.5 bg-zinc-200 dark:bg-zinc-700 hover:bg-zinc-300
                                       dark:hover:bg-zinc-600 text-zinc-800 dark:text-zinc-50 text-xs font-medium rounded-lg transition">
                                <i class="fa fa-eraser mr-2"></i> Limpiar firma
                            </button>
                            <p class="text-[11px] text-zinc-500 dark:text-zinc-400"
                                id="firma-help-preventivo-tecnico">
                                Dibuja tu firma en el área de arriba.
                            </p>
                        </div>
                    </div>
                    <input type="hidden" name="firma_tecnico" id="firma-input-preventivo-tecnico">
                    @error('firma_tecnico')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Firma Cliente (opcional) --}}
                <div class="space-y-2">
                    <p class="text-xs text-zinc-600 dark:text-zinc-300">
                        Firma del cliente <span class="font-semibold">(opcional)</span> para validar la mantención.
                    </p>
                    <div
                        class="border border-dashed border-zinc-300 dark:border-zinc-600 rounded-lg p-4 bg-zinc-50 dark:bg-zinc-900">
                        <canvas id="signature-pad-preventivo-cliente"
                            class="border border-zinc-200 dark:border-zinc-700 rounded bg-white"
                            style="height: 180px; width: 100%;"></canvas>
                        <div class="mt-3 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                            <button type="button" id="clear-signature-preventivo-cliente"
                                class="inline-flex items-center px-3 py-1.5 bg-zinc-200 dark:bg-zinc-700 hover:bg-zinc-300
                                       dark:hover:bg-zinc-600 text-zinc-800 dark:text-zinc-50 text-xs font-medium rounded-lg transition">
                                <i class="fa fa-eraser mr-2"></i> Limpiar firma
                            </button>
                            <p class="text-[11px] text-zinc-500 dark:text-zinc-400"
                                id="firma-help-preventivo-cliente">
                                Solicita al representante del cliente que firme aquí.
                            </p>
                        </div>
                    </div>
                    <input type="hidden" name="firma_cliente" id="firma-input-preventivo-cliente">
                    <div class="space-y-1">
                        <label for="firma_cliente_nombre"
                            class="block text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase">
                            Nombre del representante
                        </label>
                        <input type="text" name="firma_cliente_nombre" id="firma_cliente_nombre"
                            value="{{ old('firma_cliente_nombre') }}"
                            class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-950 text-sm text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                            placeholder="Ej: Juan Pérez" maxlength="150">
                        @error('firma_cliente_nombre')
                            <p class="text-red-500 text-xs">{{ $message }}</p>
                        @enderror
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400">
                            Registra el nombre de quien firma por el cliente.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Acciones --}}
        <div class="flex justify-end gap-3 pt-3 border-t border-zinc-100 dark:border-zinc-700 mt-2">
            <a href="{{ route('informes.index') }}"
                class="bg-zinc-200 dark:bg-zinc-700 hover:bg-zinc-300 dark:hover:bg-zinc-600 text-zinc-800 dark:text-white font-semibold px-5 py-2 rounded-lg text-xs sm:text-sm transition inline-flex items-center gap-2">
                <i class="fa fa-arrow-left text-xs"></i>
                Volver al listado
            </a>
            <button type="submit"
                class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-6 py-2 rounded-lg text-xs sm:text-sm shadow-sm transition inline-flex items-center gap-2">
                <i class="fa fa-save text-xs"></i>
                Generar informe preventivo
            </button>
        </div>

    </div>
</form>
