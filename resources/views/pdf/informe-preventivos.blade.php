<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Informe Preventivo - {{ $informe->numero_reporte_servicio }}</title>
    <style>
        /* Márgenes de página para impresión (respetado por la mayoría de engines) */
        @page {
            size: A4;
            margin: 4mm;
        }

        html,
        body {
            height: 100%;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 4mm;
            font-size: 9px;
            line-height: 1.3;
            color: #333;
        }

        /* Resto de estilos (con box-sizing para que padding no aumente ancho total) */
        *,
        *:before,
        *:after {
            box-sizing: inherit;
        }

        h1,
        h2 {
            color: #333;
            margin-bottom: 8px;
            text-align: center;
        }

        h1 {
            font-size: 16px;
        }

        h2 {
            font-size: 13px;
            margin-top: 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 4px 5px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #f5f5f5;
            font-weight: bold;
            font-size: 9px;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .small {
            font-size: 8px;
        }

        .lightblue {
            background-color: lightblue;
        }

        /* Cabecera (logo + datos empresa) */
        .header-band {
            width: 100%;
            /* ocupa todo el ancho disponible */
            border: 2px solid #000;
            background-color: lightblue;
            padding: 6px 10px;
            margin-bottom: 14px;
            box-sizing: border-box;
            /* padding no amplía ancho */
            position: relative;
            height: 58px;
        }

        .header-band img {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 40px;
            height: auto;
            display: block;
        }

        .header-band h1 {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -60%);
            margin: 0;
            font-size: 14px;
            line-height: 1;
            white-space: nowrap;
        }

        .header-company {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            text-align: right;
            font-size: 10px;
            line-height: 1.1;
        }

        /* Firmas */
        .firmas-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
        }

        .firmas-table td {
            border: none;
            padding: 0 10px 0 0;
            vertical-align: top;
        }

        .firma-box {
            border: 1px dashed #666;
            width: 200px;
            height: 40px;
            padding: 4px;
            background-color: #fff;
            margin-bottom: 6px;
        }

        .firma-box img {
            max-width: 150px;
            max-height: 50px;
        }

        .firma-label {
            font-weight: bold;
            margin: 0;
            font-size: 10px;
        }

        .firma-text {
            margin: 2px 0 0;
        }

        @media print {
            th {
                background-color: lightblue !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                font-weight: bold;
            }

            .lightblue {
                background-color: lightblue !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
        }
    </style>
</head>

<body>
    {{-- CABECERA --}}

    <div style="width: 96%; padding: 8px 12px; box-sizing: border-box;
            position: relative; height: 58px;">

        <!-- Logo a la izquierda (absoluto para no interferir con el centrado del título) -->
        <img src="{{ $base64Logo }}" alt="Logo Empresa"
            style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
                width: 40px; height: auto; display: block;">

        <!-- Título exactamente centrado respecto al ancho del contenedor -->
        <h1
            style="position: absolute; left: 50%; top: 50%; transform: translate(-50%, -60%);
               margin: 0; font-size: 18px; line-height: 1; white-space: nowrap;">
            PROTOCOLO MANTENCION FRESENIUS
        </h1>

        <!-- Bloque de información a la derecha, alineado verticalmente y a la derecha -->
        <div
            style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
                text-align: right; font-size: 8px; line-height: 1.1;">
            Técnicos en Equipos Médicos SpA.<br>
            Castellón 970<br>
            4030282 Concepción<br>
            R. del Biobío, Chile
        </div>
    </div>


    {{-- DATOS GENERALES --}}
    <table>
        <tbody>
            <tr>
                <td class="bold lightblue">Fecha</td>
                <td>{{ $informe->fecha->format('d/m/Y') }}</td>
                <td class="bold lightblue">Nombre del Técnico</td>
                <td>{{ $informe->usuario->name }}</td>
                <td class="bold lightblue">Cliente</td>
                <td>{{ $informe->centroMedico->centro_dialisis }}</td>
            </tr>
            <tr>
                <td class="bold lightblue">Marca/Modelo</td>
                <td>{{ $informe->equipo->marca }} / {{ $informe->equipo->modelo }}</td>
                <td class="bold lightblue">N° de Serie</td>
                <td>{{ $informe->equipo->numero_serie }}</td>
                <td class="bold lightblue">ID/N° de Inventario</td>
                <td>{{ $informe->equipo->id }}</td>
            </tr>
            <tr>
                <td class="bold lightblue">N° Reporte de Servicio</td>
                <td>{{ $informe->numero_reporte_servicio }}</td>
                <td class="bold lightblue">N° Desc. Trabajo</td>
                <td>{{ $informe->numero_reporte_servicio }}</td>
                <td class="bold lightblue">Horas de Operación</td>
                <td>{{ $informe->equipo->horas_uso }}</td>
            </tr>
        </tbody>
    </table>

    {{-- INSPECCIONES --}}
    <table>
        <thead>
            <tr>
                <th class="lightblue">N°</th>
                <th class="lightblue">Descripción</th>
                <th class="lightblue">Sí</th>
                <th class="lightblue">No</th>
                <th class="lightblue">N/A</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($informe->inspecciones as $index => $inspeccion)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $inspeccion->descripcion }}</td>
                    <td class="center">{{ $inspeccion->respuesta === 'SI' ? 'X' : '' }}</td>
                    <td class="center">{{ $inspeccion->respuesta === 'NO' ? 'X' : '' }}</td>
                    <td class="center">{{ $inspeccion->respuesta === 'N/A' ? 'X' : '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- COMENTARIOS Y PRÓXIMO CONTROL --}}
    <table>
        <tbody>
            <tr>
                <td class="bold lightblue" style="width: 25%;">Comentarios</td>
                <td style="width: 75%;">
                    {{ $informe->comentarios ?: 'Sin comentarios' }}
                </td>
            </tr>
            <tr>
                <td class="bold lightblue">Fecha Próximo Control</td>
                <td>
                    {{ $informe->fecha_proximo_control ? $informe->fecha_proximo_control->format('d/m/Y') : 'No especificada' }}
                </td>
            </tr>
        </tbody>
    </table>

    {{-- FIRMAS --}}
    <table class="firmas-table">
        <tr>
            {{-- Firma Técnico --}}
            <td>
                <div class="firma-box">
                    @if ($informe->firma_tecnico)
                        <img src="{{ $informe->firma_tecnico }}" alt="Firma Técnico">
                    @endif
                </div>
                <p class="firma-label">Técnico Responsable</p>
                <p class="firma-text small">Nombre: {{ $informe->usuario->name }}</p>
            </td>

            {{-- Firma Cliente --}}
            <td>
                <div class="firma-box">
                    @if ($informe->firma_cliente)
                        <img src="{{ $informe->firma_cliente }}" alt="Firma Cliente">
                    @endif
                </div>
                <p class="firma-label">Cliente / Representante Legal</p>
                <p class="firma-text small">
                    Nombre: {{ $informe->firma_cliente_nombre ?: '________________________' }}
                </p>
            </td>
        </tr>
    </table>
</body>

</html>
