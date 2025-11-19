<!-- Formulario Correctivo -->
<div x-show="tab === 'correctivo'">
    <form id="form-correctivo" method="POST" action="{{ route('informes.correctivo.store') }}" class="max-w-5xl mx-auto">
        @csrf

        {{-- Errores generales --}}
        @if ($errors->any() && old('modo') !== 'preventivo')
            <div
                class="mb-4 p-3 rounded-lg bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-200 text-sm border border-red-200 dark:border-red-700">
                <div class="flex items-center gap-2 mb-1">
                    <i class="fa fa-triangle-exclamation"></i>
                    <b>Corrige los siguientes campos del informe correctivo:</b>
                </div>
                <ul class="list-disc ml-5 space-y-0.5">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <input type="hidden" name="modo" value="correctivo">

        <div
            class="bg-white dark:bg-zinc-800 rounded-xl shadow-md border border-zinc-200 dark:border-zinc-700 p-6 md:p-7 space-y-6">

            {{-- Encabezado --}}
            <div
                class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 border-b border-zinc-100 dark:border-zinc-700 pb-3">
                <div>
                    <h3 class="text-xl font-bold text-zinc-900 dark:text-zinc-50 flex items-center gap-2">
                        <i class="fa fa-screwdriver-wrench text-orange-500"></i>
                        Informe Correctivo
                    </h3>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                        Registra atenciones por fallas o incidentes inesperados en el equipo.
                    </p>
                </div>
                <span
                    class="text-[11px] uppercase tracking-wide px-3 py-1 rounded-full bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-200 inline-flex items-center gap-1">
                    <i class="fa fa-bolt text-[10px]"></i>
                    Correctivo
                </span>
            </div>

            {{-- Cliente / Centro médico --}}
            <div class="space-y-4">
                <h4 class="text-sm font-semibold text-zinc-700 dark:text-zinc-200 flex items-center gap-2">
                    <span class="w-1 h-4 rounded-full bg-orange-500"></span>
                    Cliente y centro médico
                </h4>

                <div class="grid md:grid-cols-2 gap-4">
                    {{-- Cliente --}}
                    <div>
                        <label for="cliente_id"
                            class="block text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase mb-1.5">
                            Cliente
                        </label>
                        <select id="cliente_id" name="cliente_id"
                            class="select2 w-full px-3 py-2.5 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50
                                   dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 text-sm focus:outline-none focus:ring-2
                                   focus:ring-blue-500 focus:border-blue-500"
                            required>
                            <option value="">Selecciona un cliente…</option>
                            @foreach ($clientes as $cliente)
                                <option value="{{ $cliente->id }}" @selected(old('cliente_id') == $cliente->id)>
                                    {{ $cliente->nombre }} ({{ $cliente->rut }})
                                </option>
                            @endforeach
                        </select>
                        @error('cliente_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Centro Médico --}}
                    <div>
                        <label for="centro_medico_id"
                            class="block text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase mb-1.5">
                            Centro médico
                        </label>
                        <select id="centro_medico_id" name="centro_medico_id"
                            class="select2 w-full px-3 py-2.5 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50
                                   dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 text-sm focus:outline-none focus:ring-2
                                   focus:ring-blue-500 focus:border-blue-500"
                            {{ old('cliente_id') ? '' : 'disabled' }} required>
                            <option value="">Selecciona un centro médico…</option>
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
                </div>
            </div>

            {{-- Fechas y horas --}}
            <div class="space-y-4">
                <h4 class="text-sm font-semibold text-zinc-700 dark:text-zinc-200 flex items-center gap-2">
                    <span class="w-1 h-4 rounded-full bg-blue-500"></span>
                    Tiempos de atención
                </h4>

                <div class="grid lg:grid-cols-4 md:grid-cols-2 gap-4">
                    {{-- Fecha notificación --}}
                    <div>
                        <label for="fecha_notificacion"
                            class="block text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase mb-1.5">
                            Fecha de notificación
                        </label>
                        <input id="fecha_notificacion" type="date" name="fecha_notificacion"
                            value="{{ old('fecha_notificacion') }}"
                            class="w-full px-3 py-2.5 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50
                                   dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 text-sm focus:outline-none focus:ring-2
                                   focus:ring-blue-500 focus:border-blue-500"
                            required>
                        @error('fecha_notificacion')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Fecha servicio --}}
                    <div>
                        <label for="fecha_servicio"
                            class="block text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase mb-1.5">
                            Fecha de servicio
                        </label>
                        <input id="fecha_servicio" type="date" name="fecha_servicio"
                            value="{{ old('fecha_servicio') }}"
                            class="w-full px-3 py-2.5 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50
                                   dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 text-sm focus:outline-none focus:ring-2
                                   focus:ring-blue-500 focus:border-blue-500"
                            required>
                        @error('fecha_servicio')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Hora inicio --}}
                    <div>
                        <label for="hora_inicio"
                            class="block text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase mb-1.5">
                            Hora de inicio
                        </label>
                        <input id="hora_inicio" type="time" name="hora_inicio" value="{{ old('hora_inicio') }}"
                            class="w-full px-3 py-2.5 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50
                                   dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 text-sm focus:outline-none focus:ring-2
                                   focus:ring-blue-500 focus:border-blue-500"
                            required>
                        @error('hora_inicio')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Hora cierre --}}
                    <div>
                        <label for="hora_cierre"
                            class="block text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase mb-1.5">
                            Hora de cierre
                        </label>
                        <input id="hora_cierre" type="time" name="hora_cierre" value="{{ old('hora_cierre') }}"
                            class="w-full px-3 py-2.5 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50
                                   dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 text-sm focus:outline-none focus:ring-2
                                   focus:ring-blue-500 focus:border-blue-500"
                            required>
                        @error('hora_cierre')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Problema informado --}}
            <div class="space-y-2">
                <h4 class="text-sm font-semibold text-zinc-700 dark:text-zinc-200 flex items-center gap-2">
                    <span class="w-1 h-4 rounded-full bg-red-500"></span>
                    Detalle del problema
                </h4>
                <div>
                    <label for="problema_informado"
                        class="block text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase mb-1.5">
                        Problema informado
                    </label>
                    <textarea id="problema_informado" name="problema_informado" rows="3"
                        class="w-full px-3 py-2.5 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50
                               dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 text-sm focus:outline-none focus:ring-2
                               focus:ring-blue-500 focus:border-blue-500"
                        required>{{ old('problema_informado') }}</textarea>
                    @error('problema_informado')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Equipo + horas de uso --}}
            <div class="space-y-4">
                <h4 class="text-sm font-semibold text-zinc-700 dark:text-zinc-200 flex items-center gap-2">
                    <span class="w-1 h-4 rounded-full bg-emerald-500"></span>
                    Equipo intervenido
                </h4>

                <div class="grid md:grid-cols-2 gap-4">
                    {{-- Equipo --}}
                    <div>
                        <label for="equipo_id"
                            class="block text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase mb-1.5">
                            Equipo
                        </label>
                        <select id="equipo_id" name="equipo_id"
                            class="select2 w-full px-3 py-2.5 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50
                                   dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 text-sm focus:outline-none focus:ring-2
                                   focus:ring-blue-500 focus:border-blue-500"
                            {{ old('centro_medico_id') ? '' : 'disabled' }} required>
                            <option value="">Selecciona un equipo…</option>
                            @foreach ($equipos as $equipo)
                                <option value="{{ $equipo->id }}"
                                    data-numero-serie="{{ $equipo->numero_serie ?? '' }}"
                                    data-horas-uso="{{ $equipo->horas_uso ?? '' }}" @selected(old('equipo_id') == $equipo->id)>
                                    {{ $equipo->nombre }} ({{ $equipo->codigo }})
                                </option>
                            @endforeach
                        </select>
                        @error('equipo_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- N° de serie --}}
                    <div>
                        <label for="numero_serie_correctivo"
                            class="block text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase mb-1.5">
                            N° de Serie
                        </label>
                        <input id="numero_serie_correctivo" type="text" name="numero_serie"
                            value="{{ old('numero_serie') }}" readonly
                            class="w-full px-3 py-2.5 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-100
                                   dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 text-sm focus:outline-none cursor-not-allowed"
                            placeholder="Se completa al seleccionar el equipo">
                        @error('numero_serie')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Horas de uso --}}
                    <div>
                        <label for="horas_uso"
                            class="block text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase mb-1.5">
                            Horas de uso actuales
                        </label>
                        <input id="horas_uso" type="number" min="0" name="horas_uso"
                            value="{{ old('horas_uso') }}"
                            class="w-full px-3 py-2.5 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50
                                   dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 text-sm focus:outline-none focus:ring-2
                                   focus:ring-blue-500 focus:border-blue-500"
                            required>
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-1">
                            Debe ser mayor o igual a las horas actuales del equipo.
                        </p>
                        @error('horas_uso')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Trabajo realizado --}}
            <div class="space-y-2">
                <h4 class="text-sm font-semibold text-zinc-700 dark:text-zinc-200 flex items-center gap-2">
                    <span class="w-1 h-4 rounded-full bg-green-500"></span>
                    Trabajo realizado
                </h4>
                <div>
                    <label for="trabajo_realizado"
                        class="block text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase mb-1.5">
                        Descripción de la intervención
                    </label>
                    <textarea id="trabajo_realizado" name="trabajo_realizado" rows="3"
                        class="w-full px-3 py-2.5 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50
                               dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 text-sm focus:outline-none focus:ring-2
                               focus:ring-blue-500 focus:border-blue-500"
                        required>{{ old('trabajo_realizado') }}</textarea>
                    @error('trabajo_realizado')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Repuestos usados --}}
            <div class="space-y-3">
                <h4 class="text-sm font-semibold text-zinc-700 dark:text-zinc-200 flex items-center gap-2">
                    <span class="w-1 h-4 rounded-full bg-yellow-500"></span>
                    Repuestos usados
                </h4>

                <div>
                    <label for="repuestos"
                        class="block text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase mb-1.5">
                        Selecciona los repuestos
                    </label>
                    <select id="repuestos" name="repuestos[]"
                        class="select2 w-full px-3 py-2.5 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50
                               dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 text-sm focus:outline-none focus:ring-2
                               focus:ring-blue-500 focus:border-blue-500"
                        multiple>
                        @foreach ($repuestos as $repuesto)
                            <option value="{{ $repuesto->id }}" @if (is_array(old('repuestos')) && in_array($repuesto->id, old('repuestos'))) selected @endif>
                                {{ $repuesto->nombre }} (Stock: {{ $repuesto->stock }})
                            </option>
                        @endforeach
                    </select>
                    @error('repuestos')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Cantidades dinámicas --}}
                <div id="cantidades-container" class="mt-2 space-y-2"></div>
            </div>

            {{-- Condición del equipo --}}
            <div class="space-y-2">
                <h4 class="text-sm font-semibold text-zinc-700 dark:text-zinc-200 flex items-center gap-2">
                    <span class="w-1 h-4 rounded-full bg-sky-500"></span>
                    Condición final del equipo
                </h4>
                <div class="flex flex-wrap gap-6 mt-1 text-sm">
                    @php
                        $condOld = old('condicion_equipo');
                    @endphp
                    <label class="inline-flex items-center gap-2 text-zinc-700 dark:text-zinc-200">
                        <input type="radio" name="condicion_equipo" value="operativo"
                            class="form-radio text-blue-600 focus:ring-blue-500"
                            {{ $condOld === 'operativo' ? 'checked' : '' }} required>
                        <span>Operativo</span>
                    </label>
                    <label class="inline-flex items-center gap-2 text-zinc-700 dark:text-zinc-200">
                        <input type="radio" name="condicion_equipo" value="en_observacion"
                            class="form-radio text-blue-600 focus:ring-blue-500"
                            {{ $condOld === 'en_observacion' ? 'checked' : '' }}>
                        <span>En observación</span>
                    </label>
                    <label class="inline-flex items-center gap-2 text-zinc-700 dark:text-zinc-200">
                        <input type="radio" name="condicion_equipo" value="fuera_de_servicio"
                            class="form-radio text-blue-600 focus:ring-blue-500"
                            {{ $condOld === 'fuera_de_servicio' ? 'checked' : '' }}>
                        <span>Fuera de servicio</span>
                    </label>
                </div>
                @error('condicion_equipo')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Firma digital --}}
            <div class="space-y-2">
                <h4 class="text-sm font-semibold text-zinc-700 dark:text-zinc-200 flex items-center gap-2">
                    <span class="w-1 h-4 rounded-full bg-purple-500"></span>
                    Firma digital
                </h4>
                <div
                    class="border border-dashed border-zinc-300 dark:border-zinc-600 rounded-lg p-4 bg-zinc-50 dark:bg-zinc-900">
                    <p class="text-xs text-zinc-600 dark:text-zinc-300 mb-2">
                        La firma digital del técnico es obligatoria. Dibuja con el mouse o lápiz sobre el área.
                    </p>
                    <canvas id="signature-pad" class="border border-zinc-200 dark:border-zinc-700 rounded bg-white"
                        style="height: 150px; width: 100%;"></canvas>
                    <div class="mt-3 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                        <button type="button" id="clear-signature"
                            class="inline-flex items-center px-3 py-1.5 bg-zinc-200 dark:bg-zinc-700 hover:bg-zinc-300
                                   dark:hover:bg-zinc-600 text-zinc-800 dark:text-zinc-50 text-xs font-medium rounded-lg transition">
                            <i class="fa fa-eraser mr-2"></i> Limpiar firma
                        </button>
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400" id="firma-help">
                            Dibuja tu firma en el área de arriba.
                        </p>
                    </div>
                </div>
                <input type="hidden" name="firma" id="firma-input">
                @error('firma')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Firma cliente (opcional) --}}
            @php
                $mostrarFirmaClienteCorrectivo = old('firma_cliente');
            @endphp
            <div class="space-y-2">
                <div class="flex items-center justify-between gap-3">
                    <h4 class="text-sm font-semibold text-zinc-700 dark:text-zinc-200 flex items-center gap-2">
                        <span class="w-1 h-4 rounded-full bg-amber-500"></span>
                        Firma del cliente (opcional)
                    </h4>
                    <button type="button" id="btn-toggle-firma-cliente-correctivo"
                        class="inline-flex items-center px-3 py-1.5 bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-200 text-xs font-semibold rounded-lg gap-2 transition hover:bg-amber-200 dark:hover:bg-amber-900/60"
                        data-label-show="Agregar firma del cliente" data-label-hide="Ocultar firma del cliente">
                        <i class="fa fa-user-pen text-[11px]"></i>
                        <span id="btn-toggle-firma-cliente-text-correctivo">
                            {{ $mostrarFirmaClienteCorrectivo ? 'Ocultar firma del cliente' : 'Agregar firma del cliente' }}
                        </span>
                    </button>
                </div>

                <div id="firma-cliente-wrapper-correctivo"
                    class="{{ $mostrarFirmaClienteCorrectivo ? '' : 'hidden' }}"
                    data-visible="{{ $mostrarFirmaClienteCorrectivo ? '1' : '0' }}">
                    <div
                        class="border border-dashed border-zinc-300 dark:border-zinc-600 rounded-lg p-4 bg-zinc-50 dark:bg-zinc-900">
                        <p class="text-xs text-zinc-600 dark:text-zinc-300 mb-2">
                            Firma del representante del cliente/centro. Úsala sólo cuando se requiera.
                        </p>
                        <canvas id="signature-pad-correctivo-cliente"
                            class="border border-zinc-200 dark:border-zinc-700 rounded bg-white"
                            style="height: 150px; width: 100%;"></canvas>
                        <div class="mt-3 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                            <button type="button" id="clear-signature-correctivo-cliente"
                                class="inline-flex items-center px-3 py-1.5 bg-zinc-200 dark:bg-zinc-700 hover:bg-zinc-300
                                       dark:hover:bg-zinc-600 text-zinc-800 dark:text-zinc-50 text-xs font-medium rounded-lg transition">
                                <i class="fa fa-eraser mr-2"></i> Limpiar firma
                            </button>
                            <p class="text-[11px] text-zinc-500 dark:text-zinc-400"
                                id="firma-help-correctivo-cliente">
                                La firma del cliente es opcional.
                            </p>
                        </div>
                    </div>
                    <input type="hidden" name="firma_cliente" id="firma-input-correctivo-cliente"
                        value="{{ old('firma_cliente') }}">
                    @error('firma_cliente')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
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
                    class="bg-orange-600 hover:bg-orange-700 text-white font-semibold px-6 py-2 rounded-lg text-xs sm:text-sm shadow-sm transition inline-flex items-center gap-2">
                    <i class="fa fa-save text-xs"></i>
                    Generar informe correctivo
                </button>
            </div>

        </div>
    </form>
</div>
