<?php

return [
    'fresenius' => [
        'sections' => [
            [
                'title' => 'Chequeos y Calibraciones',
                'items' => [
                    ['label' => 'Inspección visual y limpieza general.'],
                    ['label' => 'Revisión de cable de alimentación.'],
                    ['label' => 'Lubricación de piezas móviles y sellos externos.'],
                    ['label' => 'Soplado de módulo eléctrico e hidráulico.'],
                    ['label' => 'Cambio de kit de Mantención.'],
                    ['label' => 'Reemplazo de O’ring de acopladores del dializador.'],
                    ['label' => 'Chequeos funcionales y paso del test T1.'],
                    ['label' => 'Chequeo y Calibración de presión de entrada de agua.'],
                    ['label' => 'Chequeo y Calibración de presión de carga de cámara de balance.'],
                    ['label' => 'Chequeo y Calibración de presión de bomba de flujo.'],
                    ['label' => 'Chequeo y Calibración de presión de Desgasificación.'],
                    ['label' => 'Chequeo y Calibración de volumen bomba de UF.'],
                    ['label' => 'Chequeo y Calibración de flujo de 300 ml/min.'],
                    ['label' => 'Chequeo y Calibración de flujo de 500 ml/min.'],
                    ['label' => 'Chequeo y Calibración de flujo de 800 ml/min.'],
                    ['label' => 'Chequeo y Calibración de volumen cámara de Balance.'],
                    ['label' => 'Chequeo y Calibración de volumen Bomba de Concentrado.'],
                    ['label' => 'Chequeo y Calibración de Bomba de Bicarbonato.'],
                    ['label' => 'Chequeo y Calibración de Temperatura.'],
                    ['label' => 'Chequeo y Calibración de Conductividad con Bibag.'],
                    ['label' => 'Chequeo y Calibración de Conductividad con Bicarbonato líquido.'],
                    ['label' => 'Chequeo y Calibración presión arterial.'],
                    ['label' => 'Chequeo y Calibración presión venosa.'],
                    ['label' => 'Chequeo y Calibración sensor flujo de sangre.'],
                    ['label' => 'Chequeo y Calibración sensor detector de aire.'],
                    ['label' => 'Chequeo y Calibración de funcionamiento y revisión de módulo arterial.'],
                    ['label' => 'Chequeo y Calibración de funcionamiento y revisión de módulo bomba de heparina.'],
                    ['label' => 'Chequeo y Calibración de funcionamiento y revisión de módulo venoso.'],
                    ['label' => 'Chequeo alarma de falla de alimentación.'],
                    ['label' => 'Chequeo de cargas de batería de respaldo.'],
                    ['label' => 'Chequeo de funcionamiento de BPM.'],
                    ['label' => 'Medición puesta a tierra.'],
                    ['label' => 'Medición corriente de fuga.'],
                    ['label' => 'Reemplazo de piezas (si procede).'],
                    ['label' => 'Lubricación de ruedas.'],
                ],
            ],
        ],
    ],

    'aspirador' => [
        'sections' => [
            [
                'title' => '1. Inspección física de la unidad',
                'items' => [
                    ['label' => '1.1 Inspección de carcaza.'],
                    ['label' => '1.2 Inspección cable de alimentación.'],
                    ['label' => '1.3 Inspección de accesorios.'],
                    ['label' => '1.4 Estado de las líneas.'],
                    ['label' => '1.5 Estado de switch ON / OFF.'],
                ],
            ],
            [
                'title' => '2. Mantenciones generales',
                'items' => [
                    ['label' => '2.1 Limpieza externa e interna.'],
                    ['label' => '2.2 Lubricación de partes móviles.'],
                    ['label' => '2.3 Limpieza de vaso colector de secreciones.'],
                    ['label' => '2.4 Limpieza de filtro.'],
                    ['label' => '2.5 Limpieza de líneas.'],
                    ['label' => '2.6 Lubricación de membrana.'],
                ],
            ],
            [
                'title' => '3. Chequeos funcionales',
                'items' => [
                    ['label' => '3.1 Prueba de encendido.'],
                    ['label' => '3.2 Prueba de funcionamiento vacuómetro con instrumento externo.'],
                    ['label' => '3.3 Prueba de fugas.'],
                    ['label' => '3.4 Prueba de presión de aspiración para adulto (-80 a -120 mmHg).'],
                    ['label' => '3.5 Prueba de presión de aspiración para niños (-80 a -100 mmHg).'],
                    ['label' => '3.6 Prueba de presión de aspiración para bebés (-60 a -80 mmHg).'],
                ],
            ],
            [
                'title' => '4. Medición de seguridad eléctrica',
                'items' => [
                    ['label' => '4.1 Prueba de seguridad eléctrica.'],
                ],
            ],
        ],
    ],

    'autoclave' => [
        'sections' => [
            [
                'title' => '1. Consultas generales',
                'items' => [
                    ['label' => '1.1 Consulta del uso del equipo al personal encargado.'],
                    ['label' => '1.2 El personal que manipula el equipo está certificado.'],
                    ['label' => '1.3 Equipo cuenta con certificación.'],
                ],
            ],
            [
                'title' => '2. Inspección física de la unidad',
                'items' => [
                    ['label' => '2.1 Inspección de carcaza y estructura del equipo.'],
                    ['label' => '2.2 Inspección cable de alimentación.'],
                    ['label' => '2.3 Inspección de accesorios.'],
                    ['label' => '2.4 Inspección de display y pantalla.'],
                ],
            ],
            [
                'title' => '3. Mantenciones generales',
                'items' => [
                    ['label' => '3.1 Limpieza externa.'],
                    ['label' => '3.2 Limpieza de filtro(s).'],
                    ['label' => '3.3 Limpieza de cámara interna.'],
                    ['label' => '3.4 Limpieza de línea de desagüe.'],
                    ['label' => '3.5 Limpieza de estanque de agua destilada y de desecho.'],
                ],
            ],
            [
                'title' => '4. Verificaciones',
                'items' => [
                    ['label' => '4.1 Verificación de suministros.', 'options' => ['Bueno', 'Regular', 'Malo']],
                    ['label' => '4.2 Verificación de funcionamiento de panel frontal y leds.', 'options' => ['Bueno', 'Regular', 'Malo']],
                    ['label' => '4.3 Verificación de funcionamiento de manómetro digital o analógico.', 'options' => ['Bueno', 'Regular', 'Malo']],
                    ['label' => '4.4 Verificación de display y visual.', 'options' => ['Bueno', 'Regular', 'Malo']],
                    ['label' => '4.5 Verificación del estado de la carcaza.', 'options' => ['Bueno', 'Regular', 'Malo']],
                    ['label' => '4.6 Verificación del estado de cable de alimentación.', 'options' => ['Bueno', 'Regular', 'Malo']],
                    ['label' => '4.7 Verificación del estado de la cámara.', 'options' => ['Bueno', 'Regular', 'Malo']],
                    ['label' => '4.8 Verificación del estado de resistencia(s).', 'options' => ['Bueno', 'Regular', 'Malo']],
                    ['label' => '4.8.1 Valor de resistencia medida durante verificación (anotar valor).', 'type' => 'text', 'placeholder' => 'Valor de resistencia'],
                    ['label' => '4.9 Verificación de aislamiento de calor.', 'options' => ['Bueno', 'Regular', 'Malo']],
                    ['label' => '4.10 Verificación de estado de empaquetadura de compuerta.', 'options' => ['Bueno', 'Regular', 'Malo']],
                ],
            ],
            [
                'title' => '5. Pruebas de funcionamiento',
                'items' => [
                    ['label' => '5.1 Prueba de sellado de compuerta.'],
                    ['label' => '5.2 Verificación de funcionamiento de switch de cerradura.'],
                    ['label' => '5.3 Prueba de funcionamiento de electroválvula.'],
                    ['label' => '5.4 Funcionamiento de resistencia(s).'],
                    ['label' => '5.5 Prueba de válvula de seguridad.'],
                    ['label' => '5.6 Prueba de funcionamiento de ciclo de esterilización con carga 121°C.'],
                    ['label' => '5.7 Prueba de funcionamiento de ciclo de esterilización con carga 134°C.'],
                ],
            ],
        ],
    ],

    'balanzas' => [
        'sections' => [
            [
                'title' => '1. Inspección física y mantenciones generales',
                'items' => [
                    ['label' => '1.1 Inspección visual y condición de estructura del equipo.'],
                    ['label' => '1.2 Limpieza y remoción de polvo.'],
                    ['label' => '1.3 Lubricación de partes móviles.'],
                    ['label' => '1.4 Limpieza electrónica.'],
                    ['label' => '1.5 Mejora de contactos y conexiones (reapriete).'],
                ],
            ],
            [
                'title' => '2. Chequeos funcionales',
                'items' => [
                    ['label' => '2.1 Panel frontal.'],
                    ['label' => '2.2 Display.'],
                    ['label' => '2.3 Estabilizadores.'],
                    ['label' => '2.4 Baterías.'],
                    ['label' => '2.5 Tallímetro.'],
                ],
            ],
            [
                'title' => '3. Verificaciones',
                'items' => [
                    ['label' => '3.1 Botones de navegación.'],
                    ['label' => '3.2 Medición con peso patrón 10 KG.', 'type' => 'text', 'placeholder' => 'Registrar diferencia'],
                    ['label' => '3.3 Medición con peso patrón 20 KG.', 'type' => 'text', 'placeholder' => 'Registrar diferencia'],
                    ['label' => '3.4 Medición con peso patrón 40 KG.', 'type' => 'text', 'placeholder' => 'Registrar diferencia'],
                    ['label' => '3.5 Medición con peso patrón 80 KG.', 'type' => 'text', 'placeholder' => 'Registrar diferencia'],
                    ['label' => '3.6 Medición con peso patrón 100 KG.', 'type' => 'text', 'placeholder' => 'Registrar diferencia'],
                ],
            ],
            [
                'title' => '4. Medición seguridad eléctrica',
                'items' => [
                    ['label' => '4.1 Prueba de seguridad eléctrica.'],
                ],
            ],
        ],
    ],

    'dea' => [
        'sections' => [
            [
                'title' => '1. Inspección física de la unidad',
                'items' => [
                    ['label' => '1.1 Inspección de carcaza y estructura del equipo.'],
                    ['label' => '1.2 Inspección batería de respaldo.'],
                    ['label' => '1.3 Inspección de parches.'],
                    ['label' => '1.3.1 Parche adulto.'],
                    ['label' => '1.3.2 Parche pediátrico.'],
                    ['label' => '1.4 Inspección de cable ECG.'],
                    ['label' => '1.5 Inspección display e indicadores LED.'],
                ],
            ],
            [
                'title' => '2. Mantenciones generales',
                'items' => [
                    ['label' => '2.1 Limpieza general.'],
                    ['label' => '2.2 Prueba de encendido.'],
                    ['label' => '2.3 Batería respaldo mantiene carga.'],
                    ['label' => '2.3.1 Cambio batería respaldo.'],
                    ['label' => '2.4.1 Prueba de descarga uno (registrar Joules).', 'requires_comment' => true, 'comment_placeholder' => 'Joules', 'comment_suffix' => ' joules'],
                    ['label' => '2.4.2 Prueba de descarga dos (registrar Joules).', 'requires_comment' => true, 'comment_placeholder' => 'Joules', 'comment_suffix' => ' joules'],
                    ['label' => '2.4.3 Prueba de descarga tres (registrar Joules).', 'requires_comment' => true, 'comment_placeholder' => 'Joules', 'comment_suffix' => ' joules'],
                    ['label' => '2.4.4 Prueba de descarga cuatro (registrar Joules).', 'requires_comment' => true, 'comment_placeholder' => 'Joules', 'comment_suffix' => ' joules'],
                    [
                        'label' => '2.5 Chequeo ECG (120 ± 2 bpm).',
                        'requires_comment' => true,
                        'comment_fields' => [
                            ['key' => 'electrodos_3', 'label' => 'Electrodos (3)', 'placeholder' => 'Indique Valor'],
                            ['key' => 'electrodos_5', 'label' => 'Electrodos (5)', 'placeholder' => 'Indique Valor'],
                        ],
                    ],
                    ['label' => '2.6 Pulsos de calibración (300 ppm ± 1).'],
                    ['label' => '2.7 Alarma de frecuencia cardíaca.'],
                ],
            ],
            [
                'title' => '3. Medición de seguridad eléctrica',
                'items' => [
                    ['label' => '3.1 Prueba de seguridad eléctrica.'],
                ],
            ],
        ],
    ],
];
