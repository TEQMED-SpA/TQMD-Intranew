<x-app-layout _title="Crear Informes">

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
                class="bg-white dark:bg-zinc-900 overflow-hidden shadow-sm sm:rounded-lg border border-zinc-200 dark:border-zinc-800">
                <div class="p-6 text-gray-900 dark:text-zinc-100" x-data="{ tab: 'correctivo' }" x-cloak>

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
                            <button x-on:click="tab = 'correctivo'" type="button"
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

                            <button x-on:click="tab = 'preventivo'; initPreventivoCanvas()" type="button"
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
                            @include('informes.create-preventivo')
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
                // Select2 global
                $('.select2').select2();

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
                    canvasId: 'signature-pad-tecnico',
                    clearId: 'clear-signature-tecnico',
                    inputId: 'firma-tecnico-input',
                    helpId: 'firma-tecnico-help',
                    required: true,
                },
                {
                    canvasId: 'signature-pad-cliente',
                    clearId: 'clear-signature-cliente',
                    inputId: 'firma-cliente-input',
                    helpId: 'firma-cliente-help',
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
                if (!$clienteSelect.val()) {
                    $centroSelect.prop('disabled', true);
                    $equipoSelect.prop('disabled', true);
                } else if (!$centroSelect.val()) {
                    $centroSelect.prop('disabled', false);
                    $equipoSelect.prop('disabled', true);
                } else if (!$equipoSelect.val()) {
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

                        if (!clienteId) {
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

                        if (!centroId) {
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
        $(document).ready(function() {
            // Tab por defecto: Correctivo
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
