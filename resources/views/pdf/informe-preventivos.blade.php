<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Informe Preventivo - {{ $informe->numero_reporte_servicio }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 15px;
            font-size: 9px;
            line-height: 1.3;
            color: #333;
        }

        h1,
        h2 {
            color: #333;
            margin-bottom: 8px;
            text-align: center;
        }

        h1 {
            font-size: 16px;
            margin-top: 5px;
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
        .header-table {
            width: 100%;
            margin-bottom: 10px;
            border-collapse: collapse;
        }

        .header-table td {
            border: none;
            padding: 0;
            vertical-align: top;
        }

        .header-logo {
            width: 60px;
        }

        .header-logo img {
            max-width: 50px;
            height: auto;
        }

        .header-company {
            font-size: 10px;
            text-align: right;
            line-height: 1.25;
        }

        .header-band {
            width: 100%;
            border: 2px solid #000;
            background-color: lightblue;
            padding: 6px 8px;
            margin-bottom: 14px;
        }

        .header-band-title {
            margin: 0;
            font-size: 13px;
            text-align: center;
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
            width: 320px;
            height: 100px;
            padding: 4px;
            background-color: #fff;
            margin-bottom: 6px;
        }

        .firma-box img {
            max-width: 300px;
            max-height: 90px;
        }

        .firma-label {
            font-weight: bold;
            margin: 0;
            font-size: 9px;
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
    <table class="header-table">
        <tr>
            <td class="header-logo">
                @if (!empty($base64Logo))
                    <img src="{{ $base64Logo }}" alt="Logo Empresa">
                @endif
            </td>
            <td class="header-company">
                Técnicos en Equipos Médicos SpA.<br>
                Castellón 970<br>
                4030282 Concepción<br>
                R. del Biobío, Chile
            </td>
        </tr>
    </table>

    <div class="header-band">
        <h1 class="header-band-title">PROTOCOLO MANTENCIÓN FRESENIUS</h1>
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
                <td class="bold lightblue">Modelo/Marca</td>
                <td>{{ $informe->equipo->modelo }} / {{ $informe->equipo->marca }}</td>
                <td class="bold lightblue">N° de Serie</td>
                <td>{{ $informe->equipo->numero_serie }}</td>
                <td class="bold lightblue">N° de Inventario</td>
                <td>{{ $informe->numero_inventario }}</td>
            </tr>
            <tr>
                <td class="bold lightblue">N° Reporte de Servicio</td>
                <td colspan="3">{{ $informe->numero_reporte_servicio }}</td>
                <td class="bold lightblue">Horas de Operación</td>
                <td>{{ $informe->equipo->horas_uso }}</td>
            </tr>
        </tbody>
    </table>

    {{-- INSPECCIONES --}}
    <h2>INSPECCIONES</h2>
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
    <h2>OBSERVACIONES</h2>
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
    <h2>FIRMAS</h2>
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
