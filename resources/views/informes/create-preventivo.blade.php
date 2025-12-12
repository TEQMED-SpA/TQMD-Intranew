{{-- resources/views/informes/create-preventivo.blade.php --}}

<form id="form-preventivo" method="POST" action="{{ route('informes.preventivo.store') }}" class="max-w-5xl mx-auto">
    @csrf

    {{-- Errores generales sólo para preventivo --}}
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
                    Informe Preventivo
                </h3>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                    Registra mantenciones preventivas realizadas al equipo.
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

            {{-- Centro / Sucursal + Equipo --}}
            <div class="grid md:grid-cols-2 gap-4 mt-4">
                {{-- Centro / Sucursal --}}
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
                        {{ old('centro_medico_id') ? '' : 'disabled' }} {{-- deshabilitado si no hay centro --}} required>
                        <option value="">Selecciona un equipo…</option>
                        @foreach ($equipos as $equipo)
                            <option value="{{ $equipo->id }}" @selected(old('equipo_id') == $equipo->id)>
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
                // Valor por defecto para horas_operacion:
                // 1) old('horas_operacion') si existe
                // 2) si no, y hay old('equipo_id'), usar horas_uso del equipo seleccionado
                $horasOperacionValue = old('horas_operacion');

                if ($horasOperacionValue === null && old('equipo_id')) {
                    $equipoSeleccionado = $equipos->firstWhere('id', old('equipo_id'));
                    if ($equipoSeleccionado && !is_null($equipoSeleccionado->horas_uso)) {
                        $horasOperacionValue = (int) $equipoSeleccionado->horas_uso;
                    }
                }
            @endphp

            {{-- N° Inventario + Horas operación --}}
            <div class="grid md:grid-cols-2 gap-4 mt-4">
                {{-- N° inventario --}}
                <div>
                    <label for="numero_inventario"
                        class="block text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase mb-1.5">
                        N° Inventario
                    </label>
                    <input id="numero_inventario" type="text" name="numero_inventario"
                        value="{{ old('numero_inventario') }}"
                        class="w-full px-3 py-2.5 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50
                               dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 text-sm focus:outline-none
                               focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                        required>
                    @error('numero_inventario')
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
                                @selected(old('equipo_id') == $equipo->id)>
                                ({{ $equipo->codigo }})
                                - {{ $equipo->numero_serie }}
                            </option>
                        @endforeach
                    </select>
                    @error('equipo_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>


                {{-- Numero de serie --}}
                <div>
                    <label for="numero_serie"
                        class="block text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase mb-1.5">
                        N° de Serie
                    </label>
                    <input id="numero_serie" type="text" name="numero_serie" value="{{ old('numero_serie') }}"
                        readonly
                        class="w-full px-3 py-2.5 border border-zinc-300 dark:border-zinc-700 rounded-lg
               bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 text-sm
               focus:outline-none cursor-not-allowed"
                        required>
                    @error('numero_serie')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Horas operación --}}
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
        @php
            $inspeccionesTexto = [
                'Inspección visual y limpieza general.',
                'Revisión de cable de alimentación.',
                'Lubricación de piezas móviles y sellos externos.',
                'Soplado de módulo eléctrico e hidráulico.',
                'Cambio de kit de Mantención.',
                'Reemplazo de O’ring de acopladores del dializador.',
                'Chequeos funcionales y paso del test T1.',
                'Chequeo y Calibración de presión de entrada de agua.',
                'Chequeo y Calibración de presión de carga de cámara de balance.',
                'Chequeo y Calibración de presión de bomba de flujo.',
                'Chequeo y Calibración de presión de Desgasificación.',
                'Chequeo y Calibración de volumen bomba de UF.',
                'Chequeo y Calibración de flujo de 300 ml/min.',
                'Chequeo y Calibración de flujo de 500 ml/min.',
                'Chequeo y Calibración de flujo de 800 ml/min.',
                'Chequeo y Calibración de volumen cámara de Balance.',
                'Chequeo y Calibración de volumen Bomba de Concentrado.',
                'Chequeo y Calibración de Bomba de Bicarbonato.',
                'Chequeo y Calibración de Temperatura.',
                'Chequeo y Calibración de Conductividad con Bibag.',
                'Chequeo y Calibración de Conductividad con Bicarbonato líquido.',
                'Chequeo y Calibración presión arterial.',
                'Chequeo y Calibración presión venosa.',
                'Chequeo y Calibración sensor flujo de sangre.',
                'Chequeo y Calibración sensor detector de aire.',
                'Chequeo y Calibración de funcionamiento y revisión de módulo arterial.',
                'Chequeo y Calibración de funcionamiento y revisión de módulo bomba de heparina.',
                'Chequeo y Calibración de funcionamiento y revisión de módulo venoso.',
                'Chequeo alarma de falla de alimentación-sonido continuo-mensaje en pantalla: Falla de corriente.',
                'Chequeo de cargas de batería de respaldo.',
                'Chequeo de funcionamiento de BPM.',
                'Medición puesta a tierra.',
                'Medición corriente de fuga.',
                'Reemplazo de piezas (si procede).',
                'Lubricación de ruedas.',
            ];
        @endphp

        <div class="space-y-3">
            <h4 class="text-sm font-semibold text-zinc-700 dark:text-zinc-200 flex items-center gap-2">
                <span class="w-1 h-4 rounded-full bg-sky-500"></span>
                Inspecciones realizadas
            </h4>

            <div class="overflow-x-auto rounded-lg border border-zinc-200 dark:border-zinc-700">
                <table class="min-w-full text-xs md:text-sm">
                    <thead class="bg-zinc-100 dark:bg-zinc-800">
                        <tr>
                            <th class="px-3 py-2 text-left font-semibold text-zinc-700 dark:text-zinc-100 w-10">#
                            </th>
                            <th class="px-3 py-2 text-left font-semibold text-zinc-700 dark:text-zinc-100">
                                Descripción
                            </th>
                            <th class="px-3 py-2 text-center font-semibold text-zinc-700 dark:text-zinc-100 w-40">
                                Respuesta
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($inspeccionesTexto as $idx => $txt)
                            @php
                                $oldResp = old("inspecciones.$idx");
                            @endphp
                            <tr class="border-t border-zinc-200 dark:border-zinc-700">
                                <td class="px-3 py-2 align-top text-zinc-700 dark:text-zinc-200">
                                    {{ $idx + 1 }}
                                </td>
                                <td class="px-3 py-2 align-top text-zinc-900 dark:text-zinc-100">
                                    {{ $txt }}
                                </td>
                                <td class="px-3 py-2 align-top">
                                    <div class="flex flex-col gap-1 items-start md:items-center">
                                        <label class="inline-flex items-center gap-1 text-[11px] md:text-xs">
                                            <input type="radio" name="inspecciones[{{ $idx }}]"
                                                value="SI" class="text-emerald-600 focus:ring-emerald-500"
                                                {{ $oldResp === 'SI' ? 'checked' : '' }} required>
                                            <span>SI</span>
                                        </label>
                                        <label class="inline-flex items-center gap-1 text-[11px] md:text-xs">
                                            <input type="radio" name="inspecciones[{{ $idx }}]"
                                                value="NO" class="text-red-600 focus:ring-red-500"
                                                {{ $oldResp === 'NO' ? 'checked' : '' }}>
                                            <span>NO</span>
                                        </label>
                                        <label class="inline-flex items-center gap-1 text-[11px] md:text-xs">
                                            <input type="radio" name="inspecciones[{{ $idx }}]"
                                                value="N/A" class="text-zinc-600 focus:ring-zinc-500"
                                                {{ $oldResp === 'N/A' ? 'checked' : '' }}>
                                            <span>N/A</span>
                                        </label>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @error('inspecciones')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
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
                    <input type="hidden" name="firma_tecnico" id="firma-input-preventivo-tecnico"
                        value="{{ old('firma_tecnico') }}">
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
                    <input type="hidden" name="firma_cliente" id="firma-input-preventivo-cliente"
                        value="{{ old('firma_cliente') }}">
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
