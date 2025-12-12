<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Informe Correctivo - Folio {{ $informe->numero_folio }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 12px;
            color: #333;
        }

        h1,
        h2 {
            color: #333;
            margin-bottom: 10px;
            text-align: center;
        }

        h1 {
            font-size: 18px;
            margin-top: 10px;
        }

        h2 {
            font-size: 14px;
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px 8px;
            text-align: left;
            vertical-align: top;
        }

        .section {
            margin-bottom: 20px;
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
            font-size: 11px;
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
            font-size: 12px;
            text-align: right;
            line-height: 1.25;
        }

        /* Firmas */
        .firmas-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }

        .firmas-table td {
            border: none;
            padding: 0 10px 0 0;
            vertical-align: top;
        }

        .firma-box {
            border: 1px dashed #666;
            width: 340px;
            height: 120px;
            padding: 4px;
            background-color: #fff;
            margin-bottom: 6px;
        }

        .firma-box img {
            max-width: 320px;
            max-height: 110px;
        }

        .firma-label {
            font-weight: bold;
            margin: 0;
        }

        .firma-text {
            margin: 2px 0 0;
        }

        hr.firma-line {
            border: none;
            height: 1px;
            width: 200px;
            background-color: #333;
            margin: 4px 0;
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

    <h1>ORDEN DE SERVICIO</h1>

    {{-- DATOS GENERALES --}}
    <table>
        <tbody>
            <tr>
                <td class="bold lightblue">N° Orden</td>
                <td colspan="5">{{ $informe->numero_folio }}</td>
            </tr>
            <tr>
                <td class="bold lightblue">Cliente N°</td>
                <td>{{ $informe->cliente->id }}</td>
                <td class="bold lightblue">Nombre Comercial</td>
                <td>{{ $informe->centroMedico->centro_dialisis }}</td>
                <td class="bold lightblue">Dirección</td>
                <td>
                    {{ $informe->centroMedico->region }},
                    {{ $informe->centroMedico->ciudad }},
                    {{ $informe->centroMedico->direccion }}
                </td>
            </tr>
            <tr>
                <td class="bold lightblue">Razón Social</td>
                <td>{{ $informe->cliente->nombre }}</td>
                <td class="bold lightblue">RUT</td>
                <td>{{ $informe->cliente->rut }}</td>
                <td class="bold lightblue">Dirección Cliente</td>
                <td>{{ $informe->cliente->direccion }}</td>
            </tr>
            <tr>
                <td class="bold lightblue">Teléfono</td>
                <td>{{ $informe->centroMedico->telefono }}</td>
                <td class="bold lightblue">Fecha de Servicio</td>
                <td>{{ $informe->fecha_servicio->format('d/m/Y') }}</td>
                <td class="bold lightblue">Fecha de Notificación</td>
                <td>{{ $informe->fecha_notificacion->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td class="bold lightblue">Problema Informado</td>
                <td colspan="3">{{ $informe->problema_informado }}</td>
                <td class="bold lightblue">Nombre del Técnico</td>
                <td>{{ $informe->usuario->name }}</td>
            </tr>
        </tbody>
    </table>

    {{-- DATOS DEL EQUIPO --}}
    <h2>DATOS DEL EQUIPO</h2>
    <table>
        <tbody>
            <tr>
                <td class="bold lightblue">Serie</td>
                <td class="bold lightblue">Código ID</td>
                <td class="bold lightblue">Descripción</td>
                <td class="bold lightblue">Marca/Modelo</td>
                <td class="bold lightblue">Horas de Uso</td>
            </tr>
            <tr>
                <td>{{ $informe->equipo->numero_serie }}</td>
                <td>{{ $informe->equipo->codigo }}</td>
                <td>{{ $informe->equipo->descripcion }}</td>
                <td>{{ $informe->equipo->marca }} / {{ $informe->equipo->modelo }}</td>
                <td>{{ $informe->equipo->horas_uso }}</td>
            </tr>
        </tbody>
    </table>

    {{-- DESCRIPCIÓN ATENCIÓN --}}
    <h2>DESCRIPCIÓN DE LA ATENCIÓN</h2>
    <table>
        <tbody>
            <tr>
                <td class="bold lightblue">Hora de Inicio</td>
                <td>{{ $informe->hora_inicio }}</td>
                <td class="bold lightblue">Hora de Cierre</td>
                <td>{{ $informe->hora_cierre }}</td>
            </tr>
            <tr>
                <td class="bold lightblue">Trabajo Realizado</td>
                <td colspan="3">{{ $informe->trabajo_realizado }}</td>
            </tr>
        </tbody>
    </table>

    {{-- REPUESTOS --}}
    <h2>REPUESTOS</h2>
    <table>
        <thead>
            <tr>
                <th class="bold lightblue">Cantidad</th>
                <th class="bold lightblue">Código</th>
                <th class="bold lightblue">Descripción</th>
                <th class="bold lightblue">N° Serie</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($informe->repuestos as $repuesto)
                <tr>
                    <td>{{ $repuesto->pivot->cantidad_usada }}</td>
                    <td>{{ $repuesto->id }}</td>
                    <td>{{ $repuesto->descripcion }}</td>
                    <td>{{ $repuesto->serie }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="center">Sin repuestos utilizados</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- CONDICIÓN DEL EQUIPO --}}
    <h2>CONDICIÓN DEL EQUIPO</h2>
    <table>
        <tbody>
            <tr>
                <td class="bold lightblue" style="width: 25%;">Condición</td>
                <td style="width: 75%;">
                    {{ ucfirst(str_replace('_', ' ', $informe->condicion_equipo)) }}
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
                    @if ($informe->firma)
                        <img src="{{ $informe->firma }}" alt="Firma Técnico">
                    @endif
                </div>
                <p class="firma-label">Encargado Servicio TEQMED</p>
                <p class="firma-text small">Nombre: {{ $informe->usuario->name }}</p>
            </td>

            {{-- Firma Cliente --}}
            <td>
                @if ($informe->firma_cliente)
                    <div class="firma-box">
                        <img src="{{ $informe->firma_cliente }}" alt="Firma Cliente">
                    </div>
                    <p class="firma-label">Firma del Cliente</p>
                    <p class="firma-text small">
                        Reponsable: {{ $informe->firma_cliente_nombre ?: $informe->cliente->nombre }}
                    </p>
                @else
                    <div class="firma-box"></div>
                    <p class="firma-label">Cliente</p>
                    <hr class="firma-line">
                    <p class="small">Nombre: {{ $informe->firma_cliente_nombre ?: '' }}</p>
                    <p class="small">Cliente: {{ $informe->cliente->nombre }} (RUT: {{ $informe->cliente->rut }})</p>
                    <hr class="firma-line">
                @endif
            </td>
        </tr>
    </table>
</body>

</html>
