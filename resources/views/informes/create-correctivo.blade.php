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

            {{-- Datos generales --}}
            <div class="space-y-4">
                <h4 class="text-sm font-semibold text-zinc-700 dark:text-zinc-200 flex items-center gap-2">
                    <span class="w-1 h-4 rounded-full bg-orange-500"></span>
                    Datos generales
                </h4>

                {{-- Cliente / Centro médico --}}
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
                                   focus:ring-orange-500 focus:border-orange-500"
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

                    {{-- Centro Médico --}}
                    <div>
                        <label for="centro_medico_id"
                            class="block text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase mb-1.5">
                            Centro médico
                        </label>
                        <select id="centro_medico_id" name="centro_medico_id"
                            class="select2 w-full px-3 py-2.5 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50
                                   dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 text-sm focus:outline-none focus:ring-2
                                   focus:ring-orange-500 focus:border-orange-500"
                            required>
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

                {{-- Fechas y horas --}}
                <div class="grid lg:grid-cols-4 md:grid-cols-2 gap-4 mt-4">
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
                                   focus:ring-orange-500 focus:border-orange-500"
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
                                   focus:ring-orange-500 focus:border-orange-500"
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
                                   focus:ring-orange-500 focus:border-orange-500"
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
                                   focus:ring-orange-500 focus:border-orange-500"
                            required>
                        @error('hora_cierre')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
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
                                   focus:ring-orange-500 focus:border-orange-500"
                            required>
                            <option value="">Selecciona un equipo…</option>
                            @foreach ($equipos as $equipo)
                                <option value="{{ $equipo->id }}" @selected(old('equipo_id') == $equipo->id)>
                                    {{ $equipo->nombre }} ({{ $equipo->codigo }})
                                </option>
                            @endforeach
                        </select>
                        @error('equipo_id')
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
                                   focus:ring-orange-500 focus:border-orange-500"
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

            {{-- Problema informado --}}
            <div class="space-y-4">
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
                               focus:ring-orange-500 focus:border-orange-500"
                        required>{{ old('problema_informado') }}</textarea>
                    @error('problema_informado')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
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
                               focus:ring-orange-500 focus:border-orange-500"
                        required>{{ old('trabajo_realizado') }}</textarea>
                    @error('trabajo_realizado')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Repuestos usados --}}
            <div class="space-y-3"
                x-data='{
                    repuestos: @json(old('repuestos', [['repuesto_id' => null, 'cantidad' => null]])),
                    agregar() { this.repuestos.push({ repuesto_id: null, cantidad: null }); },
                    quitar(index) { if (this.repuestos.length > 1) { this.repuestos.splice(index, 1); } }
                }'>
                <h4 class="text-sm font-semibold text-zinc-700 dark:text-zinc-200 flex items-center gap-2">
                    <span class="w-1 h-4 rounded-full bg-amber-500"></span>
                    Repuestos utilizados
                </h4>

                <div class="space-y-3">
                    <template x-for="(item, index) in repuestos" :key="index">
                        <div
                            class="grid md:grid-cols-2 gap-4 border border-zinc-200 dark:border-zinc-700 rounded-lg p-4">
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

            {{-- Condición del equipo --}}
            <div class="space-y-4">
                <h4 class="text-sm font-semibold text-zinc-700 dark:text-zinc-200 flex items-center gap-2">
                    <span class="w-1 h-4 rounded-full bg-sky-500"></span>
                    Condición final del equipo
                </h4>
                <div>
                    <label for="condicion_equipo"
                        class="block text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase mb-1.5">
                        Condición del equipo
                    </label>
                    <select id="condicion_equipo" name="condicion_equipo"
                        class="w-full px-3 py-2.5 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50
                               dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 text-sm focus:outline-none focus:ring-2
                               focus:ring-sky-500 focus:border-sky-500"
                        required>
                        <option value="">Selecciona una opción…</option>
                        @foreach (['operativo', 'en_observacion', 'fuera_de_servicio'] as $estado)
                            <option value="{{ $estado }}" @selected(old('condicion_equipo') === $estado)>
                                {{ ucfirst(str_replace('_', ' ', $estado)) }}
                            </option>
                        @endforeach
                    </select>
                    @error('condicion_equipo')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
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
                            <canvas id="signature-pad-correctivo-tecnico"
                                class="border border-zinc-200 dark:border-zinc-700 rounded bg-white"
                                style="height: 180px; width: 100%;"></canvas>
                            <div
                                class="mt-3 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                                <button type="button" id="clear-signature-correctivo-tecnico"
                                    class="inline-flex items-center px-3 py-1.5 bg-zinc-200 dark:bg-zinc-700 hover:bg-zinc-300
                                           dark:hover:bg-zinc-600 text-zinc-800 dark:text-zinc-50 text-xs font-medium rounded-lg transition">
                                    <i class="fa fa-eraser mr-2"></i> Limpiar firma
                                </button>
                                <p class="text-[11px] text-zinc-500 dark:text-zinc-400"
                                    id="firma-help-correctivo-tecnico">
                                    Dibuja tu firma en el área de arriba.
                                </p>
                            </div>
                        </div>
                        <input type="hidden" name="firma" id="firma-input-correctivo-tecnico">
                        @error('firma')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Firma Cliente (opcional) --}}
                    <div class="space-y-2">
                        <p class="text-xs text-zinc-600 dark:text-zinc-300">
                            Firma del cliente <span class="font-semibold">(opcional)</span> para validar la atención.
                        </p>
                        <div
                            class="border border-dashed border-zinc-300 dark:border-zinc-600 rounded-lg p-4 bg-zinc-50 dark:bg-zinc-900">
                            <canvas id="signature-pad-correctivo-cliente"
                                class="border border-zinc-200 dark:border-zinc-700 rounded bg-white"
                                style="height: 180px; width: 100%;"></canvas>
                            <div
                                class="mt-3 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                                <button type="button" id="clear-signature-correctivo-cliente"
                                    class="inline-flex items-center px-3 py-1.5 bg-zinc-200 dark:bg-zinc-700 hover:bg-zinc-300
                                           dark:hover:bg-zinc-600 text-zinc-800 dark:text-zinc-50 text-xs font-medium rounded-lg transition">
                                    <i class="fa fa-eraser mr-2"></i> Limpiar firma
                                </button>
                                <p class="text-[11px] text-zinc-500 dark:text-zinc-400"
                                    id="firma-help-correctivo-cliente">
                                    Solicita al representante del cliente que firme aquí.
                                </p>
                            </div>
                        </div>
                        <input type="hidden" name="firma_cliente" id="firma-input-correctivo-cliente">
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
                    class="bg-orange-600 hover:bg-orange-700 text-white font-semibold px-6 py-2 rounded-lg text-xs sm:text-sm shadow-sm transition inline-flex items-center gap-2">
                    <i class="fa fa-save text-xs"></i>
                    Generar informe correctivo
                </button>
            </div>

        </div>
    </form>
</div>
