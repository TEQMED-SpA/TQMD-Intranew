@once
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const $clienteSelect = window.jQuery ? jQuery('#form-preventivo #cliente_id') : null;
            const $centroSelect = window.jQuery ? jQuery('#form-preventivo #centro_medico_id') : null;
            const $equipoSelect = window.jQuery ? jQuery('#form-preventivo #equipo_id') : null;
            const $horasOperacionInput = window.jQuery ? jQuery('#form-preventivo #horas_operacion') : null;
            const numeroSerieInput = document.querySelector('#form-preventivo #numero_serie');
            const tipoEquipoId = document.getElementById('form-preventivo')?.dataset?.tipoEquipoId || '';

            const enableEquipoSelect = (habilitar) => {
                if (!$equipoSelect) return;
                $equipoSelect.prop('disabled', !habilitar);
            };

            if ($clienteSelect && $centroSelect) {
                $clienteSelect.on('change', function() {
                    const clienteId = jQuery(this).val();
                    $centroSelect.empty().append('<option value="">Selecciona un centro…</option>');
                    enableEquipoSelect(false);
                    if ($equipoSelect) {
                        $equipoSelect.empty().append('<option value="">Selecciona un equipo…</option>');
                    }
                    if ($horasOperacionInput) {
                        $horasOperacionInput.val('');
                    }
                    if (numeroSerieInput) {
                        numeroSerieInput.value = '';
                    }
                    if (!clienteId) {
                        $centroSelect.prop('disabled', true);
                        return;
                    }
                    $centroSelect.prop('disabled', false);
                    jQuery.get(`/clientes/${clienteId}/centros`, function(data) {
                        (data || [])
                        .forEach((centro) => {
                            $centroSelect.append(
                                `<option value="${centro.id}">${centro.centro_dialisis}</option>`
                            );
                        });
                    });
                });

                $centroSelect.on('change', function() {
                    const centroId = jQuery(this).val();
                    if ($equipoSelect) {
                        $equipoSelect.empty().append('<option value="">Selecciona un equipo…</option>');
                    }
                    if ($horasOperacionInput) {
                        $horasOperacionInput.val('');
                    }
                    if (numeroSerieInput) {
                        numeroSerieInput.value = '';
                    }
                    if (!centroId) {
                        enableEquipoSelect(false);
                        return;
                    }
                    enableEquipoSelect(true);
                    const queryParams = tipoEquipoId ? `?tipo_equipo_id=${tipoEquipoId}` : '';
                    jQuery.get(`/centros-medicos/${centroId}/equipos${queryParams}`, function(data) {
                        (data || []).forEach((equipo) => {
                            const serie = equipo.numero_serie ? equipo.numero_serie : '';
                            const horas = typeof equipo.horas_uso !== 'undefined' && equipo
                                .horas_uso !== null ? equipo.horas_uso : '';
                            $equipoSelect.append(
                                `<option value="${equipo.id}" data-numero-serie="${serie}" data-horas-uso="${horas}">${equipo.texto}</option>`
                            );
                        });
                    });
                });
            }

            if ($equipoSelect) {
                const actualizarDatosEquipo = () => {
                    const option = $equipoSelect.find('option:selected');
                    const serie = option.data('numero-serie');
                    const horas = option.data('horas-uso');
                    if (numeroSerieInput) {
                        numeroSerieInput.value = serie || '';
                    }
                    if ($horasOperacionInput && typeof horas !== 'undefined') {
                        $horasOperacionInput.val(horas ?? '');
                    }
                    const equipoId = option.val();
                    if (equipoId) {
                        jQuery.get(`/equipos/${equipoId}/horas-uso`, function(data) {
                            const horasUso = data?.horas_uso ?? horas ?? '';
                            if ($horasOperacionInput) {
                                $horasOperacionInput.val(horasUso);
                            }
                        });
                    }
                };

                $equipoSelect.on('change', actualizarDatosEquipo);
                actualizarDatosEquipo();
            }

            const createSignaturePad = ({
                canvasId,
                clearId,
                inputId,
                helpId,
                required,
                emptyMessage,
                height = 180
            }) => {
                const canvas = document.getElementById(canvasId);
                const clearBtn = document.getElementById(clearId);
                const input = document.getElementById(inputId);
                const help = document.getElementById(helpId);
                if (!canvas || !input || typeof SignaturePad === 'undefined') {
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
                    const data = pad.toData();
                    const ratio = Math.max(window.devicePixelRatio || 1, 1);
                    const width = canvas.offsetWidth || 300;
                    canvas.width = width * ratio;
                    canvas.height = height * ratio;
                    const ctx = canvas.getContext('2d');
                    ctx.scale(ratio, ratio);
                    pad.clear();
                    if (data && data.length) {
                        pad.fromData(data);
                    }
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

                if (input && input.value) {
                    try {
                        pad.fromDataURL(input.value);
                    } catch (error) {
                        console.warn('No se pudo restaurar la firma previa', error);
                        pad.clear();
                    }
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

            const pads = [
                createSignaturePad({
                    canvasId: 'signature-pad-preventivo-tecnico',
                    clearId: 'clear-signature-preventivo-tecnico',
                    inputId: 'firma-input-preventivo-tecnico',
                    helpId: 'firma-help-preventivo-tecnico',
                    required: true,
                    emptyMessage: 'La firma del técnico es obligatoria. Dibuja tu firma.',
                }),
                createSignaturePad({
                    canvasId: 'signature-pad-preventivo-cliente',
                    clearId: 'clear-signature-preventivo-cliente',
                    inputId: 'firma-input-preventivo-cliente',
                    helpId: 'firma-help-preventivo-cliente',
                    required: false,
                    emptyMessage: '',
                }),
            ].filter(Boolean);

            const form = document.getElementById('form-preventivo');
            if (form && pads.length) {
                form.addEventListener('submit', (e) => {
                    let hasError = false;
                    pads.forEach(({
                        pad,
                        input,
                        help,
                        required,
                        defaultHelpText,
                        defaultHelpColor,
                        emptyMessage
                    }) => {
                        if (!pad) return;
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
            }
        });
    </script>
@endonce
