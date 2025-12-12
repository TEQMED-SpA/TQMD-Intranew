<x-app-layout _title="Crear Informes">

    <x-auth-session-status class="mb-4" :status="session('status')" />

    @push('styles')
        <style>
            /* Estilos personalizados para Select2 mejorados */
            .select2-container-custom .select2-selection {
                min-height: 42px !important;
                padding: 8px 12px !important;
                border: 1px solid #d1d5db !important;
                border-radius: 8px !important;
                background-color: #f9fafb !important;
                color: #111827 !important;
                font-size: 14px !important;
                transition: all 0.2s ease !important;
            }

            .select2-container-custom .select2-selection:hover {
                border-color: #9ca3af !important;
                background-color: #f3f4f6 !important;
            }

            .select2-container-custom.select2-container--focus .select2-selection {
                border-color: #f97316 !important;
                box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1) !important;
                background-color: #ffffff !important;
            }

            /* Modo oscuro */
            .dark .select2-container-custom .select2-selection {
                background-color: #18181b !important;
                border-color: #3f3f46 !important;
                color: #f4f4f5 !important;
            }

            .dark .select2-container-custom .select2-selection:hover {
                border-color: #52525b !important;
                background-color: #27272a !important;
            }

            .dark .select2-container-custom.select2-container--focus .select2-selection {
                border-color: #fb923c !important;
                box-shadow: 0 0 0 3px rgba(251, 146, 60, 0.2) !important;
                background-color: #09090b !important;
            }

            /* Dropdown mejorado */
            .select2-dropdown-custom {
                border: 1px solid #d1d5db !important;
                border-radius: 8px !important;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
                background-color: #ffffff !important;
                margin-top: 4px !important;
            }

            .dark .select2-dropdown-custom {
                background-color: #18181b !important;
                border-color: #3f3f46 !important;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3) !important;
            }

            .select2-dropdown-custom .select2-search__field {
                border: 1px solid #e5e7eb !important;
                border-radius: 6px !important;
                padding: 8px 12px !important;
                margin: 8px !important;
                background-color: #f9fafb !important;
                color: #111827 !important;
                font-size: 14px !important;
            }

            .dark .select2-dropdown-custom .select2-search__field {
                background-color: #27272a !important;
                border-color: #3f3f46 !important;
                color: #f4f4f5 !important;
            }

            .select2-dropdown-custom .select2-results__options {
                max-height: 200px !important;
                overflow-y: auto !important;
            }

            .select2-dropdown-custom .select2-results__option {
                padding: 10px 12px !important;
                font-size: 14px !important;
                color: #374151 !important;
                transition: background-color 0.15s ease !important;
            }

            .dark .select2-dropdown-custom .select2-results__option {
                color: #d4d4d8 !important;
            }

            .select2-dropdown-custom .select2-results__option:hover,
            .select2-dropdown-custom .select2-results__option--highlighted {
                background-color: #fed7aa !important;
                color: #9a3412 !important;
            }

            .dark .select2-dropdown-custom .select2-results__option:hover,
            .dark .select2-dropdown-custom .select2-results__option--highlighted {
                background-color: #7c2d12 !important;
                color: #fed7aa !important;
            }

            /* Flecha personalizada */
            .select2-container-custom .select2-selection__arrow {
                height: 40px !important;
                right: 8px !important;
            }

            .select2-container-custom .select2-selection__arrow b {
                border-color: #6b7280 transparent transparent transparent !important;
                border-width: 6px 6px 0 6px !important;
            }

            .dark .select2-container-custom .select2-selection__arrow b {
                border-color: #9ca3af transparent transparent transparent !important;
            }

            /* Placeholder */
            .select2-container-custom .select2-selection__placeholder {
                color: #9ca3af !important;
                font-size: 14px !important;
            }

            .dark .select2-container-custom .select2-selection__placeholder {
                color: #71717a !important;
            }

            /* Estado seleccionado */
            .select2-container-custom .select2-selection__choice {
                background-color: #fed7aa !important;
                color: #9a3412 !important;
                border: 1px solid #fdba74 !important;
                border-radius: 4px !important;
                padding: 2px 6px !important;
                font-size: 12px !important;
                margin: 2px !important;
            }

            .dark .select2-container-custom .select2-selection__choice {
                background-color: #7c2d12 !important;
                color: #fed7aa !important;
                border-color: #9a3412 !important;
            }

            .select2-container-custom .select2-selection__choice__remove {
                color: #9a3412 !important;
                margin-right: 4px !important;
            }

            .dark .select2-container-custom .select2-selection__choice__remove {
                color: #fed7aa !important;
            }

            .select2-container-custom .select2-selection__choice__remove:hover {
                color: #7c2d12 !important;
            }

            .dark .select2-container-custom .select2-selection__choice__remove:hover {
                color: #fdba74 !important;
            }
        </style>
    @endpush

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
                class="bg-white dark:bg-zinc-900 overflow-hidden shadow-sm sm:rounded-lg border border-zinc-200 dark:border-zinc-800">
                <div class="p-6 text-gray-900 dark:text-zinc-100" x-data="{
                    tab: window.location.hash === '#preventivos' ? 'preventivo' : 'correctivo',
                    setTab(value) {
                        this.tab = value;
                        if (value === 'preventivo') {
                            window.location.hash = 'preventivos';
                        } else {
                            history.replaceState(null, '', window.location.pathname + window.location.search);
                        }
                    }
                }" x-init="window.addEventListener('hashchange', () => {
                    if (window.location.hash === '#preventivos') {
                        tab = 'preventivo';
                    }
                })" x-cloak>

                    {{-- Header de la sección --}}
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-50 flex items-center gap-2">
                                <span>Crear Informes</span>
                            </h3>
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                                Completa los datos para generar informes <b>correctivos</b> o <b>preventivos</b> según
                                la atención realizada.
                            </p>
                        </div>
                    </div>

                    {{-- Tabs --}}
                    <div class="mb-6 border-b border-zinc-200 dark:border-zinc-700">
                        <nav class="flex gap-2">
                            <button x-on:click="setTab('correctivo')" type="button"
                                class="px-4 py-2 text-sm font-medium rounded-t-lg border-b-2 -mb-px transition"
                                x-bind:class="tab === 'correctivo'
                                    ?
                                    'border-blue-600 text-blue-600 bg-blue-50 dark:bg-blue-950/40 dark:text-blue-300' :
                                    'border-transparent text-zinc-500 hover:text-zinc-700 hover:border-zinc-300 dark:text-zinc-400 dark:hover:text-zinc-200'">
                                <span class="inline-flex items-center gap-2">
                                    <i class="fa fa-tools"></i>
                                    <span>Informe Correctivo</span>
                                </span>
                            </button>

                            <button x-on:click="setTab('preventivo')" type="button"
                                class="px-4 py-2 text-sm font-medium rounded-t-lg border-b-2 -mb-px transition"
                                x-bind:class="tab === 'preventivo'
                                    ?
                                    'border-emerald-600 text-emerald-600 bg-emerald-50 dark:bg-emerald-950/40 dark:text-emerald-300' :
                                    'border-transparent text-zinc-500 hover:text-zinc-700 hover:border-zinc-300 dark:text-zinc-400 dark:hover:text-zinc-200'">
                                <span class="inline-flex items-center gap-2">
                                    <i class="fa fa-clipboard-check"></i>
                                    <span>Informe Preventivo</span>
                                </span>
                            </button>
                        </nav>
                    </div>

                    {{-- Contenido tabs --}}
                    <div class="mt-2">
                        {{-- Correctivo --}}
                        <div x-show="tab === 'correctivo'">
                            @include('informes.create-correctivo')
                        </div>

                        {{-- Preventivo --}}
                        <div x-show="tab === 'preventivo'">
                            <div
                                class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-6 space-y-6">
                                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                    <div>
                                        <h4
                                            class="text-lg font-semibold text-zinc-900 dark:text-white flex items-center gap-2">
                                            <i class="fa fa-clipboard-check text-emerald-500"></i>
                                            Selecciona el tipo de informe preventivo
                                        </h4>
                                        <p class="text-sm text-zinc-500 dark:text-zinc-300">
                                            Elige el protocolo correspondiente para continuar al formulario específico.
                                        </p>
                                    </div>
                                    <a href="{{ route('informes.preventivos.select-tipo') }}"
                                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-200 rounded-lg hover:bg-zinc-200 dark:hover:bg-zinc-700 transition">
                                        <i class="fa fa-th-list text-xs"></i>
                                        Ver todos los tipos
                                    </a>
                                </div>

                                <div class="grid gap-4 md:grid-cols-2">
                                    @forelse ($tiposPreventivos as $tipo)
                                        <div
                                            class="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/40 p-5 flex flex-col gap-3">
                                            <div class="flex items-center gap-3">
                                                <span
                                                    class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 dark:bg-emerald-900/40 dark:text-emerald-200 text-lg font-semibold">
                                                    {{ mb_substr($tipo->nombre, 0, 2) }}
                                                </span>
                                                <div>
                                                    <p class="text-base font-semibold text-zinc-900 dark:text-white">
                                                        {{ $tipo->nombre }}
                                                    </p>
                                                    <p class="text-xs text-zinc-500 dark:text-zinc-400">
                                                        {{ $tipo->activo ? 'Disponible' : 'No disponible' }}
                                                    </p>
                                                </div>
                                            </div>
                                            <p class="text-sm text-zinc-600 dark:text-zinc-300">
                                                Completa el formulario específico para este protocolo preventivo.
                                            </p>
                                            <div class="flex justify-end">
                                                <a href="{{ route('informes.preventivos.create', $tipo) }}"
                                                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition">
                                                    <i class="fa fa-file-signature text-xs"></i>
                                                    Completar informe
                                                </a>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-span-full text-center py-10 text-zinc-600 dark:text-zinc-400">
                                            No hay tipos de informe preventivo activos.
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script Signature Pad -->
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>

    <script>
        // ==========================
        //  FIRMA / LÓGICA CORRECTIVO
        // ==========================
        const Correctivo = {
            signaturePads: [],

            init: function() {
                // Select2 mejorado con tema personalizado
                $('.select2').select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    minimumResultsForSearch: 5,
                    dropdownCssClass: 'select2-dropdown-custom',
                    containerCssClass: 'select2-container-custom'
                });

                // --------- Carga dinámica de centros médicos ----------
                const $clienteSelect = $('#form-correctivo #cliente_id');
                const $centroSelect = $('#form-correctivo #centro_medico_id');
                const $equipoSelect = $('#form-correctivo #equipo_id');

                if ($clienteSelect.length && $centroSelect.length && $equipoSelect.length) {
                    // Estado inicial
                    if (!$clienteSelect.val() || $clienteSelect.val() === '') {
                        $centroSelect.prop('disabled', true);
                        $equipoSelect.prop('disabled', true);
                    } else if (!$centroSelect.val() || $centroSelect.val() === '') {
                        $centroSelect.prop('disabled', false);
                        $equipoSelect.prop('disabled', true);
                    }

                    // --------- Cambio de cliente
                    $clienteSelect.on('change', function() {
                        const clienteId = $(this).val();

                        // Resetear centro médico y equipo
                        $centroSelect.empty()
                            .append('<option value="">Selecciona un centro médico…</option>');
                        $equipoSelect.empty()
                            .append('<option value="">Selecciona un equipo…</option>');

                        if (!clienteId || clienteId === '') {
                            $centroSelect.prop('disabled', true);
                            $equipoSelect.prop('disabled', true);
                            return;
                        }

                        $centroSelect.prop('disabled', false);
                        $equipoSelect.prop('disabled', true);

                        // Cargar centros del cliente
                        $.get(`/clientes/${clienteId}/centros`, function(data) {
                            data.forEach(c => {
                                $centroSelect.append(
                                    `<option value="${c.id}">${c.centro_dialisis}</option>`
                                );
                            });
                            $centroSelect.trigger('change.select2');
                        }).fail(function() {
                            console.error('Error al cargar centros médicos');
                        });
                    });

                    // Cambio de centro médico
                    $centroSelect.on('change', function() {
                        const centroId = $(this).val();

                        // Resetear equipo
                        $equipoSelect.empty()
                            .append('<option value="">Selecciona un equipo…</option>');

                        if (!centroId || centroId === '') {
                            $equipoSelect.prop('disabled', true);
                            return;
                        }

                        $equipoSelect.prop('disabled', false);

                        // Cargar equipos del centro médico
                        $.get(`/centros-medicos/${centroId}/equipos`, function(data) {
                            data.forEach(e => {
                                $equipoSelect.append(
                                    `<option value="${e.id}">${e.texto}</option>`
                                );
                            });
                            $equipoSelect.trigger('change.select2');
                        }).fail(function() {
                            console.error('Error al cargar equipos del centro médico');
                        });
                    });
                }

                // --------- Repuestos dinámicos ----------
                const repuestosSelect = document.getElementById('repuestos');
                const cantidadesContainer = document.getElementById('cantidades-container');

                if (repuestosSelect && cantidadesContainer) {
                    const repuestosData = @json($repuestos->keyBy('id'));

                    $('#repuestos').on('change', function() {
                        const selected = $(this).val() || [];
                        cantidadesContainer.innerHTML = '';

                        selected.forEach(function(id) {
                            const repuesto = repuestosData[id];
                            if (!repuesto) return;

                            const wrapper = document.createElement('div');
                            wrapper.className = 'mt-2';
                            wrapper.innerHTML = `
                            <label for="cantidad_${id}" class="block text-sm font-medium text-gray-700 dark:text-zinc-200">
                                Cantidad para ${repuesto.nombre} (Stock disponible: ${repuesto.stock})
                            </label>
                            <input type="number"
                                   id="cantidad_${id}"
                                   name="cantidades[]"
                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-zinc-700 shadow-sm
                                          focus:border-indigo-500 focus:ring-indigo-500 bg-zinc-50 dark:bg-zinc-900
                                          text-zinc-900 dark:text-zinc-100 text-sm"
                                   min="1"
                                   max="${repuesto.stock}"
                                   required>
                        `;
                            cantidadesContainer.appendChild(wrapper);
                        });
                    });
                }

                // --------- Validación de fechas en tiempo real ----------
                const fechaServicioInput = document.querySelector('#form-correctivo #fecha_servicio');
                const fechaNotificacionInput = document.querySelector('#form-correctivo #fecha_notificacion');

                if (fechaServicioInput) {
                    fechaServicioInput.addEventListener('change', function() {
                        const fechaServicio = new Date(this.value);
                        const hoy = new Date();
                        const maxFecha = new Date();
                        maxFecha.setDate(hoy.getDate() + 3); // Permitir hasta 3 días futuros
                        hoy.setHours(0, 0, 0, 0); // Resetear hora para comparación correcta
                        maxFecha.setHours(0, 0, 0, 0);

                        // Eliminar mensaje de error anterior si existe
                        const errorExistente = this.parentNode.querySelector('.fecha-error');
                        if (errorExistente) {
                            errorExistente.remove();
                        }

                        // Quitar clases de error
                        this.classList.remove('border-red-500', 'focus:ring-red-500');

                        if (fechaServicio < hoy) {
                            // Agregar clases de error
                            this.classList.add('border-red-500', 'focus:ring-red-500');

                            // Crear y mostrar mensaje de error
                            const errorDiv = document.createElement('div');
                            errorDiv.className = 'fecha-error text-red-500 text-xs mt-1';
                            errorDiv.textContent =
                                'La fecha de servicio no puede ser anterior a la fecha actual.';
                            this.parentNode.appendChild(errorDiv);

                            // Limpiar el valor
                            this.value = '';

                            // Mostrar alerta
                            alert(
                                'La fecha de servicio no puede ser anterior a la fecha actual. Por favor, seleccione una fecha válida.'
                            );
                        } else if (fechaServicio > maxFecha) {
                            // Agregar clases de error
                            this.classList.add('border-red-500', 'focus:ring-red-500');

                            // Crear y mostrar mensaje de error
                            const errorDiv = document.createElement('div');
                            errorDiv.className = 'fecha-error text-red-500 text-xs mt-1';
                            errorDiv.textContent =
                                'La fecha de servicio no puede ser posterior a 3 días desde hoy.';
                            this.parentNode.appendChild(errorDiv);

                            // Limpiar el valor
                            this.value = '';

                            // Mostrar alerta
                            alert(
                                'La fecha de servicio no puede ser posterior a 3 días desde hoy. Por favor, seleccione una fecha válida.'
                            );
                        }
                    });
                }

                if (fechaNotificacionInput && fechaServicioInput) {
                    fechaNotificacionInput.addEventListener('change', function() {
                        const fechaNotificacion = new Date(this.value);
                        const fechaServicio = fechaServicioInput.value ? new Date(fechaServicioInput
                            .value) : null;

                        // Eliminar mensaje de error anterior si existe
                        const errorExistente = this.parentNode.querySelector('.fecha-error');
                        if (errorExistente) {
                            errorExistente.remove();
                        }

                        // Quitar clases de error
                        this.classList.remove('border-red-500', 'focus:ring-red-500');

                        if (fechaServicio && fechaNotificacion > fechaServicio) {
                            // Agregar clases de error
                            this.classList.add('border-red-500', 'focus:ring-red-500');

                            // Crear y mostrar mensaje de error
                            const errorDiv = document.createElement('div');
                            errorDiv.className = 'fecha-error text-red-500 text-xs mt-1';
                            errorDiv.textContent =
                                'La fecha de notificación debe ser anterior o igual a la fecha de servicio.';
                            this.parentNode.appendChild(errorDiv);

                            // Limpiar el valor
                            this.value = '';

                            // Mostrar alerta
                            alert(
                                'La fecha de notificación debe ser anterior o igual a la fecha de servicio. Por favor, seleccione una fecha válida.'
                            );
                        }
                    });
                }

                // --------- Cargar horas de uso ----------
                const equipoSelect = document.querySelector('#form-correctivo #equipo_id');
                const horasUsoInput = document.getElementById('horas_uso');

                if (equipoSelect && horasUsoInput) {
                    $('#form-correctivo #equipo_id').on('change', function() {
                        const equipoId = $(this).val();
                        if (equipoId) {
                            $.get(`/equipos/${equipoId}/horas-uso`, function(data) {
                                horasUsoInput.value = data.horas_uso || 0;
                            });
                        } else {
                            horasUsoInput.value = '';
                        }
                    });
                }

                // --------- Firmas (técnico obligatorio / cliente opcional) ----------
                this.initSignaturePads();
            }
        };

        Correctivo.initSignaturePads = function() {
            if (this.signaturePads.length) {
                return;
            }

            if (typeof SignaturePad === 'undefined') {
                return;
            }

            const padsConfig = [{
                    canvasId: 'signature-pad-correctivo-tecnico',
                    clearId: 'clear-signature-correctivo-tecnico',
                    inputId: 'firma-input-correctivo-tecnico',
                    helpId: 'firma-help-correctivo-tecnico',
                    required: true,
                },
                {
                    canvasId: 'signature-pad-correctivo-cliente',
                    clearId: 'clear-signature-correctivo-cliente',
                    inputId: 'firma-input-correctivo-cliente',
                    helpId: 'firma-help-correctivo-cliente',
                    required: false,
                },
            ];

            const createPad = ({
                canvasId,
                clearId,
                inputId,
                helpId,
                required
            }) => {
                const canvas = document.getElementById(canvasId);
                const clearBtn = document.getElementById(clearId);
                const input = document.getElementById(inputId);
                const help = document.getElementById(helpId);

                if (!canvas || !input) {
                    return null;
                }

                const pad = new SignaturePad(canvas, {
                    minWidth: 1,
                    maxWidth: 3,
                    penColor: 'black',
                });

                const defaultHelpText = help ? help.textContent : '';
                const defaultHelpColor = help ? help.style.color : '';

                const resizeCanvas = () => {
                    const ratio = Math.max(window.devicePixelRatio || 1, 1);
                    const width = canvas.offsetWidth || 300;
                    const height = 150;
                    canvas.width = width * ratio;
                    canvas.height = height * ratio;
                    const ctx = canvas.getContext('2d');
                    ctx.scale(ratio, ratio);
                    pad.clear();
                };

                resizeCanvas();
                window.addEventListener('resize', resizeCanvas);

                if (clearBtn) {
                    clearBtn.addEventListener('click', () => {
                        pad.clear();
                        input.value = '';
                        if (help) {
                            help.textContent = defaultHelpText;
                            help.style.color = defaultHelpColor;
                        }
                    });
                }

                return {
                    pad,
                    input,
                    help,
                    required,
                    defaultHelpText,
                    defaultHelpColor
                };
            };

            this.signaturePads = padsConfig
                .map(config => createPad(config))
                .filter(Boolean);

            const form = document.getElementById('form-correctivo');
            if (!form || !this.signaturePads.length) {
                return;
            }

            form.addEventListener('submit', (e) => {
                let hasError = false;

                this.signaturePads.forEach(({
                    pad,
                    input,
                    help,
                    required,
                    defaultHelpText,
                    defaultHelpColor
                }) => {
                    if (!pad) {
                        return;
                    }

                    if (pad.isEmpty()) {
                        input.value = '';
                        if (required) {
                            hasError = true;
                            if (help) {
                                help.textContent =
                                    'La firma del técnico es obligatoria. Dibuja tu firma.';
                                help.style.color = 'red';
                            }
                        } else if (help) {
                            help.textContent = defaultHelpText;
                            help.style.color = defaultHelpColor;
                        }
                    } else {
                        input.value = pad.toDataURL('image/png');
                        if (help) {
                            help.textContent = defaultHelpText;
                            help.style.color = defaultHelpColor;
                        }
                    }
                });

                if (hasError) {
                    e.preventDefault();
                }
            }, {
                once: true
            });
        };

        // ==========================
        //  FIRMA / LÓGICA PREVENTIVO
        // ==========================
        const Preventivo = {
            signaturePads: [],

            init: function() {
                const $clienteSelect = $('#form-preventivo #cliente_id');
                const $centroSelect = $('#form-preventivo #centro_medico_id');
                const $equipoSelect = $('#form-preventivo #equipo_id');
                const $horasOperacionInput = $('#form-preventivo #horas_operacion');

                // --------- Estado inicial (según old()) ----------
                if (!$clienteSelect.val() || $clienteSelect.val() === '') {
                    $centroSelect.prop('disabled', true);
                    $equipoSelect.prop('disabled', true);
                } else if (!$centroSelect.val() || $centroSelect.val() === '') {
                    $centroSelect.prop('disabled', false);
                    $equipoSelect.prop('disabled', true);
                } else if (!$equipoSelect.val() || $equipoSelect.val() === '') {
                    $centroSelect.prop('disabled', false);
                    $equipoSelect.prop('disabled', true);
                }

                // --------- Cambio de CLIENTE → carga centros ----------
                if ($clienteSelect.length && $centroSelect.length && $equipoSelect.length) {
                    $clienteSelect.on('change', function() {
                        const clienteId = $(this).val();

                        // reset centro/equipo/horas
                        $centroSelect.empty()
                            .append('<option value="">Selecciona un centro…</option>');
                        $equipoSelect.empty()
                            .append('<option value="">Selecciona un equipo…</option>')
                            .prop('disabled', true);
                        $horasOperacionInput.val('');

                        if (!clienteId || clienteId === '') {
                            $centroSelect.prop('disabled', true);
                            return;
                        }

                        $centroSelect.prop('disabled', false);

                        // endpoint que debe devolver los centros del cliente
                        $.get(`/clientes/${clienteId}/centros`, function(data) {
                            // data: [{id, centro_dialisis}, ...]
                            data.forEach(c => {
                                $centroSelect.append(
                                    `<option value="${c.id}">${c.centro_dialisis}</option>`
                                );
                            });
                        });
                    });

                    // --------- Cambio de CENTRO → carga equipos ----------
                    $centroSelect.on('change', function() {
                        const centroId = $(this).val();

                        $equipoSelect.empty()
                            .append('<option value="">Selecciona un equipo…</option>');
                        $horasOperacionInput.val('');

                        if (!centroId || centroId === '') {
                            $equipoSelect.prop('disabled', true);
                            return;
                        }

                        $equipoSelect.prop('disabled', false);

                        // endpoint que debe devolver los equipos del centro
                        $.get(`/centros-medicos/${centroId}/equipos`, function(data) {
                            // data: [{id, nombre, codigo}, ...]
                            data.forEach(e => {
                                $equipoSelect.append(
                                    `<option value="${e.id}">${e.nombre} (${e.codigo})</option>`
                                );
                            });
                        });
                    });
                }

                // --------- Cargar horas de operación al seleccionar equipo ----------
                const equipoSelect = document.querySelector('#form-preventivo #equipo_id');
                const horasOperacionInput = document.querySelector('#form-preventivo #horas_operacion');

                if (equipoSelect && horasOperacionInput) {
                    $('#form-preventivo #equipo_id').on('change', function() {
                        const equipoId = $(this).val();
                        if (equipoId) {
                            $.get(`/equipos/${equipoId}/horas-uso`, function(data) {
                                horasOperacionInput.value = data.horas_uso || 0;
                            });
                        } else {
                            horasOperacionInput.value = '';
                        }
                    });
                }

                // --------- Firmas (técnico obligatorio / cliente opcional) ----------
                this.initSignaturePads();
            }
        };

        Preventivo.initSignaturePads = function() {
            if (this.signaturePads.length) {
                return;
            }

            if (typeof SignaturePad === 'undefined') {
                return;
            }

            const padsConfig = [{
                    canvasId: 'signature-pad-preventivo-tecnico',
                    clearId: 'clear-signature-preventivo-tecnico',
                    inputId: 'firma-input-preventivo-tecnico',
                    helpId: 'firma-help-preventivo-tecnico',
                    required: true,
                    emptyMessage: 'La firma del técnico es obligatoria. Dibuja tu firma.',
                    height: 180,
                },
                {
                    canvasId: 'signature-pad-preventivo-cliente',
                    clearId: 'clear-signature-preventivo-cliente',
                    inputId: 'firma-input-preventivo-cliente',
                    helpId: 'firma-help-preventivo-cliente',
                    required: false,
                    emptyMessage: '',
                    height: 180,
                },
            ];

            const createPad = ({
                canvasId,
                clearId,
                inputId,
                helpId,
                required,
                emptyMessage,
                height
            }) => {
                const canvas = document.getElementById(canvasId);
                const clearBtn = document.getElementById(clearId);
                const input = document.getElementById(inputId);
                const help = document.getElementById(helpId);

                if (!canvas || !input) {
                    return null;
                }

                const pad = new SignaturePad(canvas, {
                    minWidth: 1,
                    maxWidth: 3,
                    penColor: 'black',
                });

                const defaultHelpText = help ? help.textContent : '';
                const defaultHelpColor = help ? help.style.color : '';

                const resizeCanvas = () => {
                    const ratio = Math.max(window.devicePixelRatio || 1, 1);
                    const width = canvas.offsetWidth || 300;
                    canvas.width = width * ratio;
                    canvas.height = height * ratio;
                    const ctx = canvas.getContext('2d');
                    ctx.scale(ratio, ratio);
                    pad.clear();
                };

                resizeCanvas();
                window.addEventListener('resize', resizeCanvas);

                if (clearBtn) {
                    clearBtn.addEventListener('click', () => {
                        pad.clear();
                        input.value = '';
                        if (help) {
                            help.textContent = defaultHelpText;
                            help.style.color = defaultHelpColor;
                        }
                    });
                }

                return {
                    pad,
                    input,
                    help,
                    required,
                    defaultHelpText,
                    defaultHelpColor,
                    emptyMessage
                };
            };

            this.signaturePads = padsConfig
                .map(config => createPad(config))
                .filter(Boolean);

            const form = document.getElementById('form-preventivo');
            if (!form || !this.signaturePads.length) {
                return;
            }

            form.addEventListener('submit', (e) => {
                let hasError = false;

                this.signaturePads.forEach(({
                    pad,
                    input,
                    help,
                    required,
                    defaultHelpText,
                    defaultHelpColor,
                    emptyMessage
                }) => {
                    if (!pad) {
                        return;
                    }

                    if (pad.isEmpty()) {
                        input.value = '';
                        if (required) {
                            hasError = true;
                            if (help) {
                                help.textContent = emptyMessage || 'La firma es obligatoria.';
                                help.style.color = 'red';
                            }
                        } else if (help) {
                            help.textContent = defaultHelpText;
                            help.style.color = defaultHelpColor;
                        }
                    } else {
                        input.value = pad.toDataURL('image/png');
                        if (help) {
                            help.textContent = defaultHelpText;
                            help.style.color = defaultHelpColor;
                        }
                    }
                });

                if (hasError) {
                    e.preventDefault();
                }
            }, {
                once: true
            });
        };

        // Inicializar al cargar
        document.addEventListener('DOMContentLoaded', function() {
            Correctivo.init();
        });

        // Se llama desde x-on:click del tab preventivo
        function initPreventivoCanvas() {
            Preventivo.init();
        }

        // Hacer accesible la función en window (por si Alpine la evalúa ahí)
        window.initPreventivoCanvas = initPreventivoCanvas;
    </script>

</x-app-layout>
