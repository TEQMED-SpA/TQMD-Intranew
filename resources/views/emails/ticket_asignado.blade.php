<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Nuevo ticket asignado: {{ $ticket->numero_ticket }}</title>
    <meta name="color-scheme" content="light dark">
    <meta name="supported-color-schemes" content="light dark">
    <style>
        :root {
            --teqmed-blue: #003d5c;
            --teqmed-cyan: #00bcd4;
            --teqmed-gray: #e5e7eb;
            --teqmed-gray-dark: #25282c;
            --teqmed-bg: #f4f7fa;
            --teqmed-bg-dark: #181a1b;
            --teqmed-white: #fff;
            --teqmed-black: #111;
        }

        body {
            background: var(--teqmed-bg);
            color: var(--teqmed-black);
            font-family: 'Segoe UI', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            -webkit-text-size-adjust: none;
        }

        .container {
            max-width: 520px;
            margin: 40px auto;
            background: var(--teqmed-white);
            border-radius: 14px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .08);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(90deg, var(--teqmed-blue) 0%, var(--teqmed-cyan) 100%);
            padding: 20px 32px 16px 32px;
            color: var(--teqmed-white);
            border-radius: 14px 14px 0 0;
            text-align: left;
        }

        .logo {
            height: 40px;
            margin-bottom: 10px;
        }

        .title {
            font-size: 1.6em;
            font-weight: bold;
            margin: 0 0 10px 0;
            display: flex;
            align-items: center;
        }

        .emoji {
            font-size: 1.2em;
            margin-right: 10px;
        }

        .ticket-number {
            display: inline-block;
            background: var(--teqmed-cyan);
            color: #fff;
            border-radius: 8px;
            padding: 7px 20px;
            font-size: 1.13em;
            letter-spacing: 2px;
            margin-bottom: 12px;
            font-weight: bold;
        }

        .main {
            padding: 32px;
            background: var(--teqmed-white);
        }

        .info-label {
            color: var(--teqmed-blue);
            font-weight: bold;
            min-width: 120px;
            display: inline-block;
        }

        .info-row {
            margin-bottom: 10px;
            font-size: 1em;
        }

        .btn {
            display: inline-block;
            background: var(--teqmed-blue);
            color: #fff !important;
            padding: 12px 36px;
            border-radius: 8px;
            font-weight: bold;
            text-decoration: none;
            margin-top: 24px;
            font-size: 1.08em;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
            transition: background .2s;
        }

        .btn:hover {
            background: #00516e;
        }

        .footer {
            background: var(--teqmed-gray);
            color: #333;
            padding: 18px 32px;
            text-align: center;
            font-size: 13px;
            border-radius: 0 0 14px 14px;
        }

        .small-note {
            color: #666;
            font-size: 12px;
            margin-top: 24px;
            opacity: 0.87;
        }

        @media (max-width:600px) {

            .container,
            .main,
            .header,
            .footer {
                padding: 10px !important;
            }

            .main {
                padding-top: 18px !important;
            }
        }

        /* Dark mode styles */
        @media (prefers-color-scheme: dark) {
            body {
                background: var(--teqmed-bg-dark) !important;
                color: var(--teqmed-white) !important;
            }

            .container {
                background: var(--teqmed-gray-dark) !important;
                color: var(--teqmed-white) !important;
                box-shadow: 0 2px 12px rgba(0, 0, 0, .25) !important;
            }

            .header {
                background: linear-gradient(90deg, #003d5c 0%, #00bcd4 100%) !important;
                color: #fff !important;
            }

            .main {
                background: var(--teqmed-gray-dark) !important;
                color: var(--teqmed-white) !important;
            }

            .btn {
                background: #00bcd4 !important;
                color: #fff !important;
            }

            .info-label {
                color: #00bcd4 !important;
            }

            .footer {
                background: #23262a !important;
                color: #bbb !important;
            }

            .ticket-number {
                background: #00bcd4 !important;
                color: #fff !important;
            }
        }

        /* Thunderbird-specific dark mode fixes */
        @media (prefers-color-scheme: dark) {

            .main,
            .container {
                background-color: #181a1b !important;
                color: #fff !important;
            }

            .header {
                background-color: #003d5c !important;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('favicon.ico') }}" alt="TEQMED Logo" class="logo">
            <div class="title">
                <span class="emoji">🔔</span>
                Nuevo ticket asignado
            </div>
            <div class="ticket-number">Ticket: {{ $ticket->numero_ticket }}</div>
            <div style="font-size:13px;opacity:.93;">
                Asignado a: <b>{{ $ticket->tecnicoAsignado->name ?? 'Técnico' }}</b>
            </div>
        </div>
        <div class="main">
            <p style="margin-top:0;">Hola <b>{{ $ticket->tecnicoAsignado->name ?? 'técnico' }}</b>,</p>
            <p style="margin-bottom:22px;">
                Se te ha asignado el siguiente ticket para atención.<br>
                Puedes ver la información pública del ticket en el siguiente enlace:
            </p>
            <div class="info-row"><span class="info-label">Cliente:</span> {{ $ticket->cliente }}</div>
            <div class="info-row"><span class="info-label">Solicitante:</span> {{ $ticket->nombre_apellido }}</div>
            <div class="info-row"><span class="info-label">Equipo:</span> {{ $ticket->modelo_maquina }}</div>
            @if ($ticket->id_numero_equipo)
                <div class="info-row"><span class="info-label">ID Equipo:</span> {{ $ticket->id_numero_equipo }}</div>
            @endif
            <div class="info-row"><span class="info-label">Falla:</span> {{ $ticket->falla_presentada }}</div>
            <div class="info-row"><span class="info-label">Estado:</span> {{ ucfirst($ticket->estado) }}</div>
            @if ($ticket->fecha_visita)
                <div class="info-row"><span class="info-label">Fecha visita:</span>
                    {{ \Carbon\Carbon::parse($ticket->fecha_visita)->format('d/m/Y H:i') }} hrs</div>
            @endif
            <div style="margin-top:20px;">
                <a class="btn" href="https://llamados.teqmed.cl/{{ $ticket->numero_ticket }}">Ver ticket público</a>
            </div>
            <div class="small-note">
                Si tienes consultas, contacta a Soporte TEQMED.<br>
                <b>Por favor, no responder a este correo.</b>
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} TEQMED SpA | Técnicos en equipos médicos SpA. Todos los derechos reservados.
        </div>
    </div>
</body>

</html>
