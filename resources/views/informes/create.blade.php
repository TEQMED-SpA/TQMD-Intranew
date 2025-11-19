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
        const Correctivo = {
            signaturePad: null,
            signaturePadCliente: null,
            initialized: false,

            init() {
                if (this.initialized) {
                    return;
                }
                this.initialized = true;

                const repuestosSelect = document.getElementById('repuestos');
                const cantidadesContainer = document.getElementById('cantidades-container');
                const repuestosData = @json($repuestos->keyBy('id'));

                $('.select2').select2();

                if (repuestosSelect && cantidadesContainer) {
                    $('#repuestos').on('change', function() {
                        const selected = $(this).val() || [];
                        cantidadesContainer.innerHTML = '';

                        selected.forEach((id) => {
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

                const $form = $('#form-correctivo');
                const $clienteSelect = $form.find('#cliente_id');
                const $centroSelect = $form.find('#centro_medico_id');
                const $equipoSelect = $form.find('#equipo_id');
                const $horasUsoInput = $form.find('#horas_uso');
                const $numeroSerieInput = $form.find('#numero_serie_correctivo');
                const $firmaClienteWrapper = $('#firma-cliente-wrapper-correctivo');
                const $firmaClienteInput = $('#firma-input-correctivo-cliente');
                const $toggleClienteBtn = $('#btn-toggle-firma-cliente-correctivo');
                const $toggleClienteText = $('#btn-toggle-firma-cliente-text-correctivo');

                const refreshSelect2 = ($select) => {
                    if (!$select.length) return;
                    if ($select.hasClass('select2-hidden-accessible')) {
                        $select.trigger('change.select2');
                    } else {
                        $select.trigger('change');
                    }
                };

                const toggleSelect = ($select, disabled) => {
                    $select.prop('disabled', disabled);
                    refreshSelect2($select);
                };

                const resetEquipoFields = () => {
                    $horasUsoInput.val('');
                    if ($numeroSerieInput.length) {
                        $numeroSerieInput.val('');
                    }
                };

                const setNumeroSerieFromSelected = () => {
                    if (!$numeroSerieInput.length) return;
                    const serie = $equipoSelect.find('option:selected').data('numero-serie') || '';
                    $numeroSerieInput.val(serie);
                };

                const setHorasFromSelectedOption = () => {
                    const horas = $equipoSelect.find('option:selected').data('horas-uso');
                    if (typeof horas !== 'undefined' && horas !== '') {
                        $horasUsoInput.val(horas);
                    }
                };

                if (!$clienteSelect.val()) {
                    toggleSelect($centroSelect, true);
                    toggleSelect($equipoSelect, true);
                } else if (!$centroSelect.val()) {
                    toggleSelect($centroSelect, false);
                    toggleSelect($equipoSelect, true);
                } else {
                    toggleSelect($centroSelect, false);
                    toggleSelect($equipoSelect, false);
                    setNumeroSerieFromSelected();
                }

                $clienteSelect.on('change', function() {
                    const clienteId = $(this).val();

                    $centroSelect.empty().append('<option value="">Selecciona un centro médico…</option>');
                    toggleSelect($centroSelect, true);

                    $equipoSelect.empty().append('<option value="">Selecciona un equipo…</option>');
                    toggleSelect($equipoSelect, true);
                    resetEquipoFields();

                    if (!clienteId) {
                        return;
                    }

                    $.get(`/clientes/${clienteId}/centros`, function(data) {
                        data.forEach((c) => {
                            $centroSelect.append(
                                `<option value="${c.id}">${c.centro_dialisis}</option>`);
                        });
                        toggleSelect($centroSelect, false);
                    });
                });

                $centroSelect.on('change', function() {
                    const centroId = $(this).val();

                    $equipoSelect.empty().append('<option value="">Selecciona un equipo…</option>');
                    toggleSelect($equipoSelect, true);
                    resetEquipoFields();

                    if (!centroId) {
                        return;
                    }

                    $.get(`/centros-medicos/${centroId}/equipos`, function(data) {
                        data.forEach((e) => {
                            const nombre = e.nombre || 'Equipo';
                            const codigo = e.codigo ? ` (${e.codigo})` : '';
                            const numeroSerie = e.numero_serie ?? '';
                            const horasUso = e.horas_uso ?? '';
                            $equipoSelect.append(
                                `<option value="${e.id}" data-numero-serie="${numeroSerie}" data-horas-uso="${horasUso}">${nombre}${codigo}</option>`
                            );
                        });
                        toggleSelect($equipoSelect, false);
                    });
                });

                $equipoSelect.on('change', function() {
                    setNumeroSerieFromSelected();
                    const equipoId = $(this).val();

                    if (!equipoId) {
                        resetEquipoFields();
                        return;
                    }

                    setHorasFromSelectedOption();

                    $.get(`/equipos/${equipoId}/horas-uso`, function(data) {
                        if (data && typeof data.horas_uso !== 'undefined') {
                            $horasUsoInput.val(data.horas_uso ?? '');
                        }
                    });
                });

                const canvasCorrectivo = document.getElementById('signature-pad');
                const clearButtonCorrectivo = document.getElementById('clear-signature');
                const firmaInputCorrectivo = document.getElementById('firma-input');
                const firmaHelpCorrectivo = document.getElementById('firma-help');
                const formCorrectivoEl = document.getElementById('form-correctivo');

                if (canvasCorrectivo && typeof SignaturePad !== 'undefined') {
                    this.signaturePad = new SignaturePad(canvasCorrectivo, {
                        minWidth: 1,
                        maxWidth: 3,
                        penColor: 'black',
                    });

                    const resizeCanvasCorrectivo = () => {
                        const ratio = Math.max(window.devicePixelRatio || 1, 1);
                        const width = canvasCorrectivo.offsetWidth || 300;
                        const height = 150;
                        canvasCorrectivo.width = width * ratio;
                        canvasCorrectivo.height = height * ratio;
                        const ctx = canvasCorrectivo.getContext('2d');
                        ctx.scale(ratio, ratio);
                        this.signaturePad.clear();
                    };

                    resizeCanvasCorrectivo();
                    window.addEventListener('resize', resizeCanvasCorrectivo);

                    if (clearButtonCorrectivo && firmaInputCorrectivo && firmaHelpCorrectivo) {
                        clearButtonCorrectivo.addEventListener('click', () => {
                            this.signaturePad.clear();
                            firmaInputCorrectivo.value = '';
                            firmaHelpCorrectivo.textContent = 'Dibuja tu firma en el área de arriba.';
                            firmaHelpCorrectivo.style.color = 'gray';
                        });
                    }

                    if (formCorrectivoEl && firmaInputCorrectivo && firmaHelpCorrectivo) {
                        formCorrectivoEl.addEventListener('submit', (e) => {
                            if (this.signaturePad.isEmpty()) {
                                e.preventDefault();
                                firmaHelpCorrectivo.textContent =
                                    'La firma digital es obligatoria. Dibuja tu firma.';
                                firmaHelpCorrectivo.style.color = 'red';
                                return;
                            }

                            firmaInputCorrectivo.value = this.signaturePad.toDataURL('image/png');

                            if ($firmaClienteWrapper.attr('data-visible') === '1' && this
                                .signaturePadCliente &&
                                !this.signaturePadCliente.isEmpty()) {
                                $firmaClienteInput.val(this.signaturePadCliente.toDataURL('image/png'));
                            } else if ($firmaClienteWrapper.attr('data-visible') === '0') {
                                $firmaClienteInput.val('');
                            }
                        });
                    }
                }

                const initFirmaClientePadCorrectivo = () => {
                    const canvasCliente = document.getElementById('signature-pad-correctivo-cliente');
                    const clearButtonCliente = document.getElementById('clear-signature-correctivo-cliente');
                    const firmaHelpCliente = document.getElementById('firma-help-correctivo-cliente');

                    if (!canvasCliente || typeof SignaturePad === 'undefined') {
                        return;
                    }

                    this.signaturePadCliente = new SignaturePad(canvasCliente, {
                        minWidth: 1,
                        maxWidth: 3,
                        penColor: 'black',
                    });

                    const resizeCanvasCliente = () => {
                        const ratio = Math.max(window.devicePixelRatio || 1, 1);
                        const width = canvasCliente.offsetWidth || 300;
                        const height = 150;
                        canvasCliente.width = width * ratio;
                        canvasCliente.height = height * ratio;
                        const ctx = canvasCliente.getContext('2d');
                        ctx.scale(ratio, ratio);
                        this.signaturePadCliente.clear();
                    };

                    resizeCanvasCliente();
                    window.addEventListener('resize', resizeCanvasCliente);

                    if (clearButtonCliente && firmaHelpCliente) {
                        clearButtonCliente.addEventListener('click', () => {
                            this.signaturePadCliente.clear();
                            $firmaClienteInput.val('');
                            firmaHelpCliente.textContent = 'La firma del cliente es opcional.';
                            firmaHelpCliente.style.color = 'gray';
                        });
                    }
                };

                const showFirmaClienteCorrectivo = () => {
                    $firmaClienteWrapper.removeClass('hidden').attr('data-visible', '1');
                    $toggleClienteText.text($toggleClienteBtn.data('label-hide'));
                    if (!this.signaturePadCliente) {
                        initFirmaClientePadCorrectivo();
                    }
                };

                const hideFirmaClienteCorrectivo = () => {
                    $firmaClienteWrapper.addClass('hidden').attr('data-visible', '0');
                    $toggleClienteText.text($toggleClienteBtn.data('label-show'));
                };

                if ($firmaClienteWrapper.data('visible') === 1 || $firmaClienteInput.val()) {
                    showFirmaClienteCorrectivo();
                }

                if ($toggleClienteBtn.length) {
                    $toggleClienteBtn.on('click', function() {
                        const visible = $firmaClienteWrapper.attr('data-visible') === '1';
                        if (visible) {
                            hideFirmaClienteCorrectivo();
                        } else {
                            showFirmaClienteCorrectivo();
                        }
                    });
                }
            }
        };

        const Preventivo = {
            signaturePad: null,
            signaturePadCliente: null,
            initialized: false,

            init() {
                if (this.initialized) {
                    return;
                }
                this.initialized = true;

                const $form = $('#form-preventivo');
                const $clienteSelect = $form.find('#cliente_id');
                const $centroSelect = $form.find('#centro_medico_id');
                const $equipoSelect = $form.find('#equipo_id');
                const $numeroSerieInput = $form.find('#numero_serie_preventivo');
                const $horasOperacionInput = $form.find('#horas_operacion');
                const $firmaClienteWrapper = $('#firma-cliente-wrapper');
                const $firmaClienteInput = $('#firma-input-preventivo-cliente');
                const $toggleClienteBtn = $('#btn-toggle-firma-cliente');
                const $toggleClienteText = $('#btn-toggle-firma-cliente-text');

                const resetEquipoFields = () => {
                    $horasOperacionInput.val('');
                    $numeroSerieInput.val('');
                };

                const setNumeroSerieFromSelected = () => {
                    const serie = $equipoSelect.find('option:selected').data('numero-serie') || '';
                    $numeroSerieInput.val(serie);
                };

                const setHorasFromSelectedOption = () => {
                    const horas = $equipoSelect.find('option:selected').data('horas-uso');
                    if (typeof horas !== 'undefined' && horas !== '') {
                        $horasOperacionInput.val(horas);
                    }
                };

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

                if ($clienteSelect.length && $centroSelect.length && $equipoSelect.length) {
                    $clienteSelect.on('change', function() {
                        const clienteId = $(this).val();

                        $centroSelect.empty().append('<option value="">Selecciona un centro…</option>');
                        $equipoSelect.empty().append('<option value="">Selecciona un equipo…</option>').prop(
                            'disabled', true);
                        resetEquipoFields();

                        if (!clienteId) {
                            $centroSelect.prop('disabled', true);
                            return;
                        }

                        $centroSelect.prop('disabled', false);

                        $.get(`/clientes/${clienteId}/centros`, function(data) {
                            data.forEach((c) => {
                                $centroSelect.append(
                                    `<option value="${c.id}">${c.centro_dialisis}</option>`);
                            });
                        });
                    });

                    $centroSelect.on('change', function() {
                        const centroId = $(this).val();

                        $equipoSelect.empty().append('<option value="">Selecciona un equipo…</option>');
                        resetEquipoFields();

                        if (!centroId) {
                            $equipoSelect.prop('disabled', true);
                            return;
                        }

                        $equipoSelect.prop('disabled', false);

                        $.get(`/centros-medicos/${centroId}/equipos`, function(data) {
                            data.forEach((e) => {
                                const nombre = e.nombre || 'Equipo';
                                const codigo = e.codigo ? ` (${e.codigo})` : '';
                                const numeroSerie = e.numero_serie ?? '';
                                const horasUso = e.horas_uso ?? '';
                                $equipoSelect.append(
                                    `<option value="${e.id}" data-numero-serie="${numeroSerie}" data-horas-uso="${horasUso}">${nombre}${codigo}</option>`
                                );
                            });
                            $equipoSelect.prop('disabled', false);
                        });
                    });
                }

                $equipoSelect.on('change', function() {
                    const equipoId = $(this).val();

                    setNumeroSerieFromSelected();

                    if (equipoId) {
                        setHorasFromSelectedOption();
                        $.get(`/equipos/${equipoId}/horas-uso`, function(data) {
                            $horasOperacionInput.val(data.horas_uso || 0);
                        });
                    } else {
                        resetEquipoFields();
                    }
                });

                const canvas = document.getElementById('signature-pad-preventivo');
                const clearButton = document.getElementById('clear-signature-preventivo');
                const firmaInput = document.getElementById('firma-input-preventivo');
                const firmaHelp = document.getElementById('firma-help-preventivo');

                if (canvas && typeof SignaturePad !== 'undefined') {
                    this.signaturePad = new SignaturePad(canvas, {
                        minWidth: 1,
                        maxWidth: 3,
                        penColor: 'black',
                    });

                    const resizeCanvasPreventivo = () => {
                        const ratio = Math.max(window.devicePixelRatio || 1, 1);
                        const width = canvas.offsetWidth || 300;
                        const height = 200;
                        canvas.width = width * ratio;
                        canvas.height = height * ratio;
                        const ctx = canvas.getContext('2d');
                        ctx.scale(ratio, ratio);
                        this.signaturePad.clear();
                    };

                    resizeCanvasPreventivo();
                    window.addEventListener('resize', resizeCanvasPreventivo);

                    if (clearButton && firmaInput && firmaHelp) {
                        clearButton.addEventListener('click', () => {
                            this.signaturePad.clear();
                            firmaInput.value = '';
                            firmaHelp.textContent = 'Dibuja tu firma en el área de arriba.';
                            firmaHelp.style.color = 'gray';
                        });
                    }

                    const form = document.getElementById('form-preventivo');
                    if (form && firmaInput && firmaHelp) {
                        form.addEventListener('submit', (e) => {
                            if (this.signaturePad.isEmpty()) {
                                e.preventDefault();
                                firmaHelp.textContent =
                                    'La firma del técnico es obligatoria. Dibuja tu firma.';
                                firmaHelp.style.color = 'red';
                                return;
                            }
                            firmaInput.value = this.signaturePad.toDataURL('image/png');

                            if ($firmaClienteWrapper.attr('data-visible') === '1' && this
                                .signaturePadCliente && !this.signaturePadCliente.isEmpty()) {
                                $firmaClienteInput.val(this.signaturePadCliente.toDataURL('image/png'));
                            } else if ($firmaClienteWrapper.attr('data-visible') === '0') {
                                $firmaClienteInput.val('');
                            }
                        });
                    }
                }

                const initFirmaClientePad = () => {
                    const canvas = document.getElementById('signature-pad-preventivo-cliente');
                    const clearButton = document.getElementById('clear-signature-preventivo-cliente');
                    const firmaHelp = document.getElementById('firma-help-preventivo-cliente');

                    if (!canvas || typeof SignaturePad === 'undefined') {
                        return;
                    }

                    this.signaturePadCliente = new SignaturePad(canvas, {
                        minWidth: 1,
                        maxWidth: 3,
                        penColor: 'black',
                    });

                    const resizeCanvas = () => {
                        const ratio = Math.max(window.devicePixelRatio || 1, 1);
                        const width = canvas.offsetWidth || 300;
                        const height = 160;
                        canvas.width = width * ratio;
                        canvas.height = height * ratio;
                        const ctx = canvas.getContext('2d');
                        ctx.scale(ratio, ratio);
                        this.signaturePadCliente.clear();
                        if ($firmaClienteInput.val()) {
                            // no hay forma directa de cargar base64 al pad sin libs extra, así que mantenemos input
                        }
                    };

                    resizeCanvas();
                    window.addEventListener('resize', resizeCanvas);

                    if (clearButton && firmaHelp) {
                        clearButton.addEventListener('click', () => {
                            this.signaturePadCliente.clear();
                            $firmaClienteInput.val('');
                            firmaHelp.textContent =
                                'La firma del cliente es opcional. Dibuja si corresponde.';
                            firmaHelp.style.color = 'gray';
                        });
                    }
                };

                const showFirmaCliente = () => {
                    $firmaClienteWrapper.removeClass('hidden').attr('data-visible', '1');
                    $toggleClienteText.text($toggleClienteBtn.data('label-hide'));
                    if (!this.signaturePadCliente) {
                        initFirmaClientePad();
                    }
                };

                const hideFirmaCliente = () => {
                    $firmaClienteWrapper.addClass('hidden').attr('data-visible', '0');
                    $toggleClienteText.text($toggleClienteBtn.data('label-show'));
                };

                if ($firmaClienteWrapper.data('visible') === 1 || $firmaClienteInput.val()) {
                    showFirmaCliente();
                }

                if ($toggleClienteBtn.length) {
                    $toggleClienteBtn.on('click', function() {
                        const visible = $firmaClienteWrapper.attr('data-visible') === '1';
                        if (visible) {
                            hideFirmaCliente();
                        } else {
                            showFirmaCliente();
                        }
                    });
                }
            }
        };

        $(document).ready(function() {
            Correctivo.init();
        });

        function initPreventivoCanvas() {
            Preventivo.init();
        }

        window.initPreventivoCanvas = initPreventivoCanvas;
    </script>

</x-app-layout>
