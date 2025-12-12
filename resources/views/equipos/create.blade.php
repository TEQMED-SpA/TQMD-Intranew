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
                {{-- Contenedor de validación en tiempo real --}}
                <div id="validation-errors" class="hidden mb-6">
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-sm">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-red-500 text-xl"></i>
                            </div>
                            <div class="ml-3 flex-1">
                                <h3 class="text-sm font-medium text-red-800">
                                    Errores de validación detectados
                                </h3>
                                <div id="error-list" class="mt-2 text-sm text-red-700">
                                    <!-- Los errores se insertarán aquí dinámicamente -->
                                </div>
                            </div>
                            <div class="ml-auto pl-3">
                                <button onclick="clearValidationErrors()"
                                    class="text-red-500 hover:text-red-700 transition-colors">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                @if (session('error'))
                    <div class="mb-4 text-red-500 text-sm">{{ session('error') }}</div>
                @endif

                <form method="POST" action="{{ route('equipos.store') }}" class="grid gap-4" id="form-equipo-create">
                    @csrf

                    {{-- Ubicación y asignación --}}
                    <div class="border-t border-zinc-200 dark:border-zinc-700 pt-6">
                        <h3 class="text-lg font-semibold text-zinc-800 dark:text-white mb-4 flex items-center gap-2">
                            <i class="fas fa-map-marker-alt text-blue-500"></i>
                            Ubicación y asignación
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="cliente_id"
                                    class="block text-zinc-700 dark:text-zinc-200 font-semibold mb-2">Cliente</label>
                                <select id="cliente_id" name="cliente_id"
                                    class="w-full px-4 py-3 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                    required>
                                    <option value="">Seleccione un cliente</option>
                                    @foreach ($clientes as $c)
                                        <option value="{{ $c->id }}" @selected(old('cliente_id') == $c->id)>
                                            {{ $c->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('cliente_id')
                                    <div class="text-red-500 text-sm mt-2 flex items-center gap-1">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div>
                                <label for="tipo_equipo_id"
                                    class="block text-zinc-700 dark:text-zinc-200 font-semibold mb-2">Tipo de
                                    equipo</label>
                                <select id="tipo_equipo_id" name="tipo_equipo_id"
                                    class="w-full px-4 py-3 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all min-w-[200px]">
                                    <option value="">Selecciona una categoría</option>
                                    @foreach ($tipos_equipo as $tipo)
                                        <option value="{{ $tipo->id }}" @selected(old('tipo_equipo_id') == $tipo->id)>
                                            {{ $tipo->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('tipo_equipo_id')
                                    <div class="text-red-500 text-sm mt-2 flex items-center gap-1">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-6 mt-6">
                            <div>
                                <label for="centro_medico_id"
                                    class="block text-zinc-700 dark:text-zinc-200 font-semibold mb-2">Centro médico /
                                    Sucursal</label>
                                <select id="centro_medico_id" name="centro_medico_id"
                                    class="w-full px-4 py-3 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                    required>
                                    <option value="">Seleccione un cliente primero</option>
                                </select>
                                @error('centro_medico_id')
                                    <div class="text-red-500 text-sm mt-2 flex items-center gap-1">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Identificación del equipo --}}
                    <div class="border-t border-zinc-200 dark:border-zinc-700 pt-6">
                        <h3 class="text-lg font-semibold text-zinc-800 dark:text-white mb-4 flex items-center gap-2">
                            <i class="fas fa-tag text-blue-500"></i>
                            Identificación del equipo
                        </h3>
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label for="nombre"
                                    class="block text-zinc-700 dark:text-zinc-200 font-semibold mb-2">Nombre del equipo
                                    <span class="text-red-500">*</span></label>
                                <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}"
                                    placeholder="Ingrese el nombre del equipo"
                                    class="w-full px-4 py-3 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                    required>
                                @error('nombre')
                                    <div class="text-red-500 text-sm mt-2 flex items-center gap-1">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="marca"
                                    class="block text-zinc-700 dark:text-zinc-200 font-semibold mb-2">Marca</label>
                                <input name="marca" id="marca" value="{{ old('marca') }}"
                                    placeholder="Ej: Siemens, GE, Philips"
                                    class="w-full px-4 py-3 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                @error('marca')
                                    <div class="text-red-500 text-sm mt-2 flex items-center gap-1">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div>
                                <label for="modelo"
                                    class="block text-zinc-700 dark:text-zinc-200 font-semibold mb-2">Modelo</label>
                                <input name="modelo" id="modelo" value="{{ old('modelo') }}"
                                    placeholder="Ej: XR-2000, Optima CT660"
                                    class="w-full px-4 py-3 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                @error('modelo')
                                    <div class="text-red-500 text-sm mt-2 flex items-center gap-1">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div>
                                <label for="codigo" class="block text-zinc-700 dark:text-zinc-200 font-semibold mb-2">
                                    Código (interno) <span class="text-red-500">*</span>
                                </label>
                                <input name="codigo" id="codigo" value="{{ old('codigo', $equipo->codigo ?? '') }}"
                                    placeholder="Código único de identificación"
                                    class="w-full px-4 py-3 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                    maxlength="80" required>
                                @error('codigo')
                                    <div class="text-red-500 text-sm mt-2 flex items-center gap-1">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Información técnica --}}
                    <div class="border-t border-zinc-200 dark:border-zinc-700 pt-6">
                        <h3 class="text-lg font-semibold text-zinc-800 dark:text-white mb-4 flex items-center gap-2">
                            <i class="fas fa-cogs text-blue-500"></i>
                            Información técnica
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="id_maquina"
                                    class="block text-zinc-700 dark:text-zinc-200 font-semibold mb-2">ID máquina
                                    (centro)</label>
                                <input name="id_maquina" id="id_maquina" value="{{ old('id_maquina') }}"
                                    placeholder="ID asignado por el centro médico"
                                    class="w-full px-4 py-3 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                @error('id_maquina')
                                    <div class="text-red-500 text-sm mt-2 flex items-center gap-1">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div>
                                <label for="numero_serie"
                                    class="block text-zinc-700 dark:text-zinc-200 font-semibold mb-2">Nº serie</label>
                                <input name="numero_serie" id="numero_serie" value="{{ old('numero_serie') }}"
                                    placeholder="Número de serie del fabricante"
                                    class="w-full px-4 py-3 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                @error('numero_serie')
                                    <div class="text-red-500 text-sm mt-2 flex items-center gap-1">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div>
                                <label for="horas_uso"
                                    class="block text-zinc-700 dark:text-zinc-200 font-semibold mb-2">Horas de
                                    uso</label>
                                <input type="number" min="0" name="horas_uso" id="horas_uso"
                                    value="{{ old('horas_uso', 0) }}" placeholder="0"
                                    class="w-full px-4 py-3 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                @error('horas_uso')
                                    <div class="text-red-500 text-sm mt-2 flex items-center gap-1">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Estado y mantenimiento --}}
                    <div class="border-t border-zinc-200 dark:border-zinc-700 pt-6">
                        <h3 class="text-lg font-semibold text-zinc-800 dark:text-white mb-4 flex items-center gap-2">
                            <i class="fas fa-heartbeat text-blue-500"></i>
                            Estado y mantenimiento
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="flex flex-col">
                                <label for="estado"
                                    class="block text-zinc-700 dark:text-zinc-200 font-semibold mb-2">Estado
                                    actual</label>
                                <select name="estado" id="estado"
                                    class="w-full px-4 py-3 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                    <option value="">Seleccione un estado</option>
                                    @foreach (['Operativo', 'En observacion', 'Fuera de servicio', 'Baja'] as $e)
                                        <option value="{{ $e }}" @selected(old('estado') === $e)>
                                            {{ $e }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('estado')
                                    <div class="text-red-500 text-sm mt-2 flex items-center gap-1">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div>
                                <label for="tipo_mantencion"
                                    class="block text-zinc-700 dark:text-zinc-200 font-semibold mb-2">Tipo de
                                    mantención</label>
                                <select name="tipo_mantencion" id="tipo_mantencion"
                                    class="w-full px-4 py-3 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                    <option value="">Seleccione tipo</option>
                                    @foreach (['T1', 'T2', 'T3'] as $t)
                                        <option value="{{ $t }}" @selected(old('tipo_mantencion', $equipo->tipo_mantencion ?? '') === $t)>
                                            {{ $t }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('tipo_mantencion')
                                    <div class="text-red-500 text-sm mt-2 flex items-center gap-1">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        {{-- Campo condicional: Días fuera de servicio --}}
                        <div id="dias-fuera-wrapper" class="mt-4"
                            style="{{ old('estado') === 'Fuera de servicio' ? '' : 'display:none;' }}">
                            <label for="cant_dias_fuera_serv"
                                class="block text-zinc-700 dark:text-zinc-200 font-semibold mb-2">
                                <i class="fas fa-calendar-alt text-orange-500 mr-1"></i>
                                Días fuera de servicio
                            </label>
                            <input type="number" min="0" max="365" name="cant_dias_fuera_serv"
                                id="cant_dias_fuera_serv" value="{{ old('cant_dias_fuera_serv') }}"
                                placeholder="Número de días fuera de servicio"
                                class="w-full px-4 py-3 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                            @error('cant_dias_fuera_serv')
                                <div class="text-red-500 text-sm mt-2 flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                            <div>
                                <label for="ultima_mantencion"
                                    class="block text-zinc-700 dark:text-zinc-200 font-semibold mb-2">Última
                                    mantención</label>
                                <input type="date" name="ultima_mantencion" id="ultima_mantencion"
                                    value="{{ old('ultima_mantencion') }}"
                                    class="w-full px-4 py-3 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                @error('ultima_mantencion')
                                    <div class="text-red-500 text-sm mt-2 flex items-center gap-1">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div>
                                <label for="proxima_mantencion"
                                    class="block text-zinc-700 dark:text-zinc-200 font-semibold mb-2">Próxima
                                    mantención</label>
                                <input type="date" name="proxima_mantencion" id="proxima_mantencion"
                                    value="{{ old('proxima_mantencion') }}"
                                    class="w-full px-4 py-3 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                @error('proxima_mantencion')
                                    <div class="text-red-500 text-sm mt-2 flex items-center gap-1">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Descripción --}}
                    <div class="border-t border-zinc-200 dark:border-zinc-700 pt-6">
                        <h3 class="text-lg font-semibold text-zinc-800 dark:text-white mb-4 flex items-center gap-2">
                            <i class="fas fa-file-alt text-blue-500"></i>
                            Observaciones
                        </h3>
                        <div>
                            <label for="descripcion"
                                class="block text-zinc-700 dark:text-zinc-200 font-semibold mb-2">Descripción</label>
                            <textarea name="descripcion" id="descripcion" rows="4"
                                placeholder="Ingrese cualquier información adicional o notas sobre el equipo..."
                                class="w-full px-4 py-3 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all resize-vertical">{{ old('descripcion') }}</textarea>
                            <div class="text-zinc-500 text-sm mt-1">
                                <span id="char-count">0</span>/500 caracteres
                            </div>
                            @error('descripcion')
                                <div class="text-red-500 text-sm mt-2 flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    {{-- Botones de acción --}}
                    <div class="border-t border-zinc-200 dark:border-zinc-700 pt-6">
                        <div class="flex justify-between items-center">
                            <button type="button" onclick="window.history.back()"
                                class="bg-zinc-200 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-200 font-semibold px-6 py-3 rounded-lg hover:bg-zinc-300 dark:hover:bg-zinc-600 transition-all flex items-center gap-2">
                                <i class="fas fa-times"></i>
                                Cancelar
                            </button>
                            <div class="flex gap-3">
                                <button type="button" onclick="resetForm()"
                                    class="bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-200 font-semibold px-6 py-3 rounded-lg hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-all flex items-center gap-2">
                                    <i class="fas fa-redo"></i>
                                    Limpiar formulario
                                </button>
                                <button type="submit"
                                    class="bg-blue-500 text-white font-semibold px-6 py-3 rounded-lg hover:bg-blue-600 transition-all flex items-center gap-2 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                    <i class="fas fa-save"></i>
                                    Guardar equipo
                                </button>
                            </div>
                        </div>
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
                const estadoSel = document.getElementById('estado');
                const diasWrapper = document.getElementById('dias-fuera-wrapper');
                const diasInput = document.getElementById('cant_dias_fuera_serv');
                const descripcionTextarea = document.getElementById('descripcion');
                const charCount = document.getElementById('char-count');
                const validationErrorsContainer = document.getElementById('validation-errors');
                const errorList = document.getElementById('error-list');
                const form = document.getElementById('form-equipo-create');

                // Validación en tiempo real
                const validationRules = {
                    cliente_id: {
                        required: true,
                        message: 'Debe seleccionar un cliente'
                    },
                    centro_medico_id: {
                        required: true,
                        message: 'Debe seleccionar un centro médico'
                    },
                    nombre: {
                        required: true,
                        minLength: 3,
                        message: 'El nombre del equipo debe tener al menos 3 caracteres'
                    },
                    codigo: {
                        required: true,
                        minLength: 2,
                        maxLength: 80,
                        message: 'El código debe tener entre 2 y 80 caracteres'
                    },
                    horas_uso: {
                        min: 0,
                        message: 'Las horas de uso deben ser un número positivo'
                    },
                    cant_dias_fuera_serv: {
                        min: 0,
                        max: 365,
                        message: 'Los días fuera de servicio deben estar entre 0 y 365'
                    }
                };

                function validateField(fieldName, value) {
                    const rule = validationRules[fieldName];
                    if (!rule) return [];

                    const errors = [];

                    if (rule.required && (!value || value.trim() === '')) {
                        errors.push(rule.message);
                    }

                    if (rule.minLength && value && value.length < rule.minLength) {
                        errors.push(rule.message);
                    }

                    if (rule.maxLength && value && value.length > rule.maxLength) {
                        errors.push(rule.message);
                    }

                    if (rule.min !== undefined && value !== '' && parseFloat(value) < rule.min) {
                        errors.push(rule.message);
                    }

                    if (rule.max !== undefined && value !== '' && parseFloat(value) > rule.max) {
                        errors.push(rule.message);
                    }

                    return errors;
                }

                function showValidationErrors(errors) {
                    if (errors.length === 0) {
                        validationErrorsContainer.classList.add('hidden');
                        return;
                    }

                    errorList.innerHTML = errors.map(error =>
                        `<div class="flex items-center gap-2 py-1">
                            <i class="fas fa-circle text-red-500 text-xs"></i>
                            <span>${error}</span>
                        </div>`
                    ).join('');

                    validationErrorsContainer.classList.remove('hidden');

                    // Auto-ocultar después de 10 segundos
                    setTimeout(() => {
                        if (!validationErrorsContainer.classList.contains('hidden')) {
                            validationErrorsContainer.classList.add('hidden');
                        }
                    }, 10000);
                }

                function clearValidationErrors() {
                    validationErrorsContainer.classList.add('hidden');
                    errorList.innerHTML = '';
                }

                function validateForm() {
                    const errors = [];

                    // Validar cliente
                    const clienteErrors = validateField('cliente_id', clienteSel.value);
                    errors.push(...clienteErrors.map(e => `Cliente: ${e}`));

                    // Validar centro médico (solo si hay cliente seleccionado)
                    if (clienteSel.value) {
                        const centroErrors = validateField('centro_medico_id', centroSel.value);
                        errors.push(...centroErrors.map(e => `Centro médico: ${e}`));
                    }

                    // Validar nombre
                    const nombreInput = document.getElementById('nombre');
                    const nombreErrors = validateField('nombre', nombreInput.value);
                    errors.push(...nombreErrors.map(e => `Nombre: ${e}`));

                    // Validar código
                    const codigoInput = document.getElementById('codigo');
                    const codigoErrors = validateField('codigo', codigoInput.value);
                    errors.push(...codigoErrors.map(e => `Código: ${e}`));

                    // Validar horas de uso
                    const horasInput = document.getElementById('horas_uso');
                    const horasErrors = validateField('horas_uso', horasInput.value);
                    errors.push(...horasErrors.map(e => `Horas de uso: ${e}`));

                    // Validar días fuera de servicio (solo si está visible)
                    if (diasWrapper.style.display !== 'none') {
                        const diasErrors = validateField('cant_dias_fuera_serv', diasInput.value);
                        errors.push(...diasErrors.map(e => `Días fuera de servicio: ${e}`));
                    }

                    showValidationErrors(errors);
                    return errors.length === 0;
                }

                function resetSelect(sel, placeholder) {
                    sel.innerHTML = '';
                    const o = document.createElement('option');
                    o.value = '';
                    o.textContent = placeholder;
                    sel.appendChild(o);
                }

                // Character counter for description
                function updateCharCount() {
                    if (descripcionTextarea && charCount) {
                        const length = descripcionTextarea.value.length;
                        charCount.textContent = length;

                        if (length > 500) {
                            descripcionTextarea.value = descripcionTextarea.value.substring(0, 500);
                            charCount.textContent = 500;
                        }

                        if (length > 450) {
                            charCount.parentElement.classList.add('text-orange-500');
                            charCount.parentElement.classList.remove('text-zinc-500');
                        } else {
                            charCount.parentElement.classList.remove('text-orange-500');
                            charCount.parentElement.classList.add('text-zinc-500');
                        }
                    }
                }

                // Re-llenado si venimos de validación fallida
                document.addEventListener('DOMContentLoaded', async () => {
                    const oldCliente = "{{ old('cliente_id') }}";
                    const oldCentro = "{{ old('centro_medico_id') }}";

                    if (oldCliente) {
                        // Carga y luego selecciona el centro viejo si existe
                        await cargarCentros(oldCliente);
                        if (oldCentro) {
                            centroSel.value = oldCentro;
                            // Si no existe en la lista (cambio de datos), deja placeholder
                            if (!centroSel.value) {
                                resetSelect(centroSel, '— Seleccione un centro —');
                            }
                        }
                    }

                    updateCharCount();
                    toggleDias();
                });

                function toggleDias() {
                    if (!estadoSel || !diasWrapper || !diasInput) return;
                    const mostrar = estadoSel.value === 'Fuera de servicio';
                    diasWrapper.style.display = mostrar ? '' : 'none';
                    diasInput.required = mostrar;
                    diasInput.disabled = !mostrar;
                    if (!mostrar) {
                        diasInput.value = '';
                    }
                }

                // Event listeners para validación en tiempo real
                clienteSel?.addEventListener('change', () => {
                    cargarCentros(clienteSel.value);
                    validateForm();
                });

                centroSel?.addEventListener('change', validateForm);

                document.getElementById('nombre')?.addEventListener('input', validateForm);
                document.getElementById('codigo')?.addEventListener('input', validateForm);
                document.getElementById('horas_uso')?.addEventListener('input', validateForm);
                diasInput?.addEventListener('input', validateForm);

                // Validar antes de enviar el formulario
                form?.addEventListener('submit', (e) => {
                    if (!validateForm()) {
                        e.preventDefault();
                        // Scroll al contenedor de errores
                        validationErrorsContainer.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    }
                });

                // Cambio de cliente => cargar centros
                clienteSel?.addEventListener('change', async () => {
                    await cargarCentros(clienteSel.value);
                });

                estadoSel?.addEventListener('change', () => {
                    toggleDias();
                    validateForm();
                });
                toggleDias();

                // Make functions available globally
                window.resetForm = resetForm;
                window.clearValidationErrors = clearValidationErrors;
            })();
        </script>
    @endpush
</x-layouts.app>
