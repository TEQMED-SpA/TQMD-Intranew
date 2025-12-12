<x-layouts.app :title="'Informe Correctivo: ' . ($informe->numero_folio ?? 'Detalle')">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-zinc-800 dark:text-zinc-100 leading-tight">
                    Informe Correctivo · Folio {{ $informe->numero_folio }}
                </h2>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                    Orden de Servicio generada el {{ $informe->fecha_servicio->format('d/m/Y') }}
                </p>
            </div>

            <div class="flex items-center gap-2">
                @php
                    $cond = $informe->condicion_equipo;
                    $condLabel =
                        [
                            'operativo' => 'Operativo',
                            'en_observacion' => 'En observación',
                            'fuera_de_servicio' => 'Fuera de servicio',
                        ][$cond] ?? ucfirst(str_replace('_', ' ', $cond));

                    $condClass = match ($cond) {
                        'operativo' => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-200',
                        'en_observacion' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-200',
                        'fuera_de_servicio' => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-200',
                        default => 'bg-zinc-100 text-zinc-800 dark:bg-zinc-700 dark:text-zinc-100',
                    };
                @endphp

                <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $condClass }}">
                    {{ $condLabel }}
                </span>

                <a href="{{ route('informes.download', ['tipo' => 'correctivo', 'id' => $informe->id]) }}"
                    class="inline-flex items-center px-3 py-1.5 text-sm font-medium rounded-lg
                          bg-blue-600 hover:bg-blue-700 text-white shadow-sm transition">
                    <i class="fa fa-file-pdf mr-2"></i> PDF
                </a>

                <button type="button" onclick="route('informes.print', ['tipo' => 'correctivo', 'id' => $informe->id])"
                    class="inline-flex items-center px-3 py-1.5 text-sm font-medium rounded-lg
                               bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm transition">
                    <i class="fa fa-print mr-2"></i> Imprimir
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-zinc-50 dark:bg-zinc-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Resumen de orden / Cliente --}}
            <div class="bg-white dark:bg-zinc-900 shadow-sm rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <div class="flex items-center justify-between mb-4 gap-3">
                    <h1 class="text-lg md:text-xl font-bold text-zinc-900 dark:text-zinc-50 flex items-center gap-2">
                        <i class="fa fa-file-medical text-blue-500"></i>
                        Orden de Servicio
                    </h1>
                    <span
                        class="text-xs px-2 py-1 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-200">
                        N° Orden: {{ $informe->numero_folio }}
                    </span>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
                    <div>
                        <p class="text-xs uppercase text-zinc-500 dark:text-zinc-400 font-semibold">Cliente N°</p>
                        <p class="text-zinc-900 dark:text-zinc-100 font-medium">
                            {{ $informe->cliente->id }}
                        </p>
                    </div>

                    <div class="md:col-span-1 lg:col-span-2">
                        <p class="text-xs uppercase text-zinc-500 dark:text-zinc-400 font-semibold">Nombre Comercial</p>
                        <p class="text-zinc-900 dark:text-zinc-100 font-medium">
                            {{ $informe->centroMedico->centro_dialisis }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs uppercase text-zinc-500 dark:text-zinc-400 font-semibold">Razón Social</p>
                        <p class="text-zinc-900 dark:text-zinc-100 font-medium">
                            {{ $informe->cliente->nombre }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs uppercase text-zinc-500 dark:text-zinc-400 font-semibold">RUT</p>
                        <p class="text-zinc-900 dark:text-zinc-100 font-medium">
                            {{ $informe->cliente->rut }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs uppercase text-zinc-500 dark:text-zinc-400 font-semibold">Dirección</p>
                        <p class="text-zinc-900 dark:text-zinc-100 font-medium">
                            {{ $informe->cliente->direccion }}
                        </p>
                    </div>

                    <div class="md:col-span-2 lg:col-span-2">
                        <p class="text-xs uppercase text-zinc-500 dark:text-zinc-400 font-semibold">Dirección Centro</p>
                        <p class="text-zinc-900 dark:text-zinc-100">
                            {{ $informe->centroMedico->region }},
                            {{ $informe->centroMedico->ciudad }},
                            {{ $informe->centroMedico->direccion }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs uppercase text-zinc-500 dark:text-zinc-400 font-semibold">Teléfono</p>
                        <p class="text-zinc-900 dark:text-zinc-100">
                            {{ $informe->centroMedico->telefono ?: 'No registrado' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs uppercase text-zinc-500 dark:text-zinc-400 font-semibold">Fecha de Servicio
                        </p>
                        <p class="text-zinc-900 dark:text-zinc-100">
                            {{ $informe->fecha_servicio->format('d/m/Y') }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs uppercase text-zinc-500 dark:text-zinc-400 font-semibold">Fecha Notificación
                        </p>
                        <p class="text-zinc-900 dark:text-zinc-100">
                            {{ $informe->fecha_notificacion->format('d/m/Y') }}
                        </p>
                    </div>

                    <div class="md:col-span-2">
                        <p class="text-xs uppercase text-zinc-500 dark:text-zinc-400 font-semibold">Problema Informado
                        </p>
                        <p class="text-zinc-900 dark:text-zinc-100">
                            {{ $informe->problema_informado }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs uppercase text-zinc-500 dark:text-zinc-400 font-semibold">Técnico Responsable
                        </p>
                        <p class="text-zinc-900 dark:text-zinc-100 font-medium">
                            {{ $informe->usuario->name }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Datos del equipo --}}
            <div class="bg-white dark:bg-zinc-900 shadow-sm rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-50 mb-4 flex items-center gap-2">
                    <i class="fa fa-microchip text-emerald-500"></i>
                    Datos del Equipo
                </h2>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
                    <div>
                        <p class="text-xs uppercase text-zinc-500 dark:text-zinc-400 font-semibold">Serie</p>
                        <p class="text-zinc-900 dark:text-zinc-100">
                            {{ $informe->equipo->numero_serie }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs uppercase text-zinc-500 dark:text-zinc-400 font-semibold">Código ID</p>
                        <p class="text-zinc-900 dark:text-zinc-100">
                            {{ $informe->equipo->codigo }}
                        </p>
                    </div>
                    <div class="lg:col-span-1 md:col-span-2">
                        <p class="text-xs uppercase text-zinc-500 dark:text-zinc-400 font-semibold">Descripción</p>
                        <p class="text-zinc-900 dark:text-zinc-100">
                            {{ $informe->equipo->descripcion ?: 'N/A' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs uppercase text-zinc-500 dark:text-zinc-400 font-semibold">Marca / Modelo</p>
                        <p class="text-zinc-900 dark:text-zinc-100">
                            {{ $informe->equipo->marca }} / {{ $informe->equipo->modelo }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs uppercase text-zinc-500 dark:text-zinc-400 font-semibold">Horas de Uso</p>
                        <p class="text-zinc-900 dark:text-zinc-100">
                            {{ $informe->equipo->horas_uso }} h
                        </p>
                    </div>
                </div>
            </div>

            {{-- Descripción de la atención --}}
            <div class="bg-white dark:bg-zinc-900 shadow-sm rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-50 mb-4 flex items-center gap-2">
                    <i class="fa fa-clipboard-list text-indigo-500"></i>
                    Descripción de la Atención
                </h2>

                <div class="grid md:grid-cols-2 gap-4 text-sm mb-4">
                    <div>
                        <p class="text-xs uppercase text-zinc-500 dark:text-zinc-400 font-semibold">Hora de Inicio</p>
                        <p class="text-zinc-900 dark:text-zinc-100">
                            {{ \Carbon\Carbon::parse($informe->hora_inicio)->format('H:i') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs uppercase text-zinc-500 dark:text-zinc-400 font-semibold">Hora de Cierre</p>
                        <p class="text-zinc-900 dark:text-zinc-100">
                            {{ \Carbon\Carbon::parse($informe->hora_cierre)->format('H:i') }}
                        </p>
                    </div>
                </div>

                <div class="text-sm">
                    <p class="text-xs uppercase text-zinc-500 dark:text-zinc-400 font-semibold mb-1">
                        Trabajo Realizado
                    </p>
                    <div
                        class="rounded-md border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 px-3 py-2 text-zinc-900 dark:text-zinc-100 whitespace-pre-line">
                        {{ $informe->trabajo_realizado }}
                    </div>
                </div>
            </div>

            {{-- Repuestos --}}
            <div class="bg-white dark:bg-zinc-900 shadow-sm rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-50 flex items-center gap-2">
                        <i class="fa fa-cubes text-amber-500"></i>
                        Repuestos Utilizados
                    </h2>
                    <span class="text-xs text-zinc-500 dark:text-zinc-400">
                        Total: {{ $informe->repuestos->count() }} ítems
                    </span>
                </div>

                @if ($informe->repuestos->isEmpty())
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">
                        No se registraron repuestos en esta atención.
                    </p>
                @else
                    <div class="overflow-x-auto rounded-lg border border-zinc-200 dark:border-zinc-700">
                        <table class="min-w-full text-sm">
                            <thead class="bg-zinc-100 dark:bg-zinc-800">
                                <tr>
                                    <th class="px-4 py-2 text-left font-semibold text-zinc-700 dark:text-white">
                                        Cantidad
                                    </th>
                                    <th class="px-4 py-2 text-left font-semibold text-zinc-700 dark:text-white">
                                        Código
                                    </th>
                                    <th class="px-4 py-2 text-left font-semibold text-zinc-700 dark:text-white">
                                        Descripción
                                    </th>
                                    <th class="px-4 py-2 text-left font-semibold text-zinc-700 dark:text-white">
                                        N° Serie
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($informe->repuestos as $repuesto)
                                    <tr
                                        class="border-t border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-800/60 transition">
                                        <td class="px-4 py-2 text-zinc-900 dark:text-zinc-100">
                                            {{ $repuesto->pivot->cantidad_usada }}
                                        </td>
                                        <td class="px-4 py-2 text-zinc-900 dark:text-zinc-100">
                                            {{ $repuesto->id }}
                                        </td>
                                        <td class="px-4 py-2 text-zinc-900 dark:text-zinc-100">
                                            {{ $repuesto->descripcion ?? $repuesto->nombre }}
                                        </td>
                                        <td class="px-4 py-2 text-zinc-900 dark:text-zinc-100">
                                            {{ $repuesto->serie ?? '—' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- Firmas --}}
            <div class="mt-10 border border-zinc-300 dark:border-zinc-700 rounded-md p-6 bg-white dark:bg-zinc-900">

                <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-50 mb-4 flex items-center gap-2">
                    <i class="fa fa-signature text-purple-500"></i> Firmas
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">

                    {{-- Firma Técnico --}}
                    <div>
                        <p class="text-sm font-semibold text-zinc-800 dark:text-white mb-2">
                            Encargado Servicio TEQMED
                        </p>

                        <div class="border border-dashed border-zinc-400 dark:border-zinc-500 rounded-md bg-white dark:bg-white 
                        flex items-center justify-start p-2 shadow-sm"
                            style="width: 340px; height: 120px;">

                            @if ($informe->firma)
                                <img src="{{ $informe->firma }}" alt="Firma Técnico"
                                    class="object-contain max-h-full max-w-full"
                                    style="max-width: 320px; max-height: 110px;">
                            @else
                                <span class="text-xs text-zinc-500 dark:text-zinc-600">Firma no disponible</span>
                            @endif
                        </div>

                        <p class="mt-2 text-sm text-zinc-700 dark:text-zinc-300">
                            Nombre: <strong>{{ $informe->usuario->name }}</strong>
                        </p>
                    </div>

                    {{-- Firma Cliente --}}
                    <div>
                        <p class="text-sm font-semibold text-zinc-800 dark:text-white mb-2">
                            Cliente / Representante Legal
                        </p>

                        <div class="border border-dashed border-zinc-400 dark:border-zinc-500 rounded-md bg-white dark:bg-white
                        flex items-center justify-start p-2 shadow-sm"
                            style="width: 340px; height: 120px;">

                            @if ($informe->firma_cliente)
                                <img src="{{ $informe->firma_cliente }}" alt="Firma Cliente"
                                    class="object-contain max-h-full max-w-full"
                                    style="max-width: 320px; max-height: 110px;">
                            @else
                                <span class="text-xs text-zinc-500 dark:text-zinc-600">Firma no disponible</span>
                            @endif
                        </div>

                        @if ($informe->firma_cliente_nombre)
                            <p class="mt-2 text-sm text-zinc-700 dark:text-zinc-300">
                                Nombre: <strong>{{ $informe->firma_cliente_nombre }}</strong>
                            </p>
                        @else
                            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                                Nombre y firma del representante del centro de diálisis.
                            </p>
                        @endif
                    </div>

                </div>
            </div>



            {{-- Acciones inferiores --}}
            <div class="flex justify-between items-center pt-2 pb-6 mt-4">
                <a href="{{ route('informes.index') }}"
                    class="inline-flex items-center px-4 py-2 rounded-lg bg-zinc-200 dark:bg-zinc-700
                          hover:bg-zinc-300 dark:hover:bg-zinc-600 text-zinc-800 dark:text-white text-sm font-medium transition">
                    <i class="fa fa-arrow-left mr-2"></i>
                    Volver a Informes
                </a>

                <div class="flex gap-2">
                    <a href="{{ route('informes.download', ['tipo' => 'correctivo', 'id' => $informe->id]) }}"
                        class="inline-flex items-center px-4 py-2 rounded-lg bg-red-500 hover:bg-red-700
                              text-white text-sm font-medium transition">
                        <i class="fa fa-file-pdf mr-2"></i>
                        Descargar PDF
                    </a>
                    <button type="button"
                        onclick="const win = window.open('{{ route('informes.print', ['tipo' => 'correctivo', 'id' => $informe->id]) }}', '_blank'); if (win) { win.focus(); win.print(); }"
                        class="inline-flex items-center px-4 py-2 rounded-lg bg-blue-500 hover:bg-blue-700
                                   text-white text-sm font-medium transition">
                        <i class="fa fa-print mr-2"></i>
                        Imprimir
                    </button>
                </div>
            </div>
        </div>
    </div>
    </x-app-layout>
