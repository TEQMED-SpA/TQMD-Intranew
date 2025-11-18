<x-layouts.app :title="'Informe Preventivo: ' . ($informe->numero_reporte_servicio ?? 'Detalle')">
    <x-slot name="header">
        <div class="flex items-center justify-between">

            <div class="flex items-center gap-2">
                <span
                    class="px-3 py-1 text-xs font-semibold rounded-full
                             bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200">
                    Mantención Preventiva
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-zinc-50 dark:bg-zinc-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Resumen / cabecera --}}

            <div>
                <h2 class="font-semibold text-xl text-zinc-800 dark:text-white leading-tight">
                    Informe Preventivo · Reporte de Servicio N° {{ $informe->numero_reporte_servicio }}
                </h2>
                <div class="flex justify-end mt-2 mb-2 gap-2">
                    <a href="{{ route('informes.download', ['tipo' => 'preventivo', 'id' => $informe->id]) }}"
                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg
                                                          bg-red-500 hover:bg-red-600 text-white transition-colors duration-200"
                        title="Descargar PDF">
                        <i class="fa fa-file-pdf text-sm"></i>
                    </a>
                    <button type="button"
                        onclick="const win = window.open('{{ route('informes.print', ['tipo' => 'preventivo', 'id' => $informe->id]) }}', '_blank'); if (win) { win.focus(); win.print(); }"
                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg
                                                          bg-blue-500 hover:bg-blue-600 text-white transition-colors duration-200"
                        title="Imprimir Informe">
                        <i class="fa fa-print text-sm"></i>
                    </button>
                </div>


                {{-- Contenido del informe --}}
                <div
                    class="bg-white dark:bg-zinc-900 shadow-sm rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                    <div class="flex items-center justify-between mb-4 gap-3">
                        <h1
                            class="text-lg md:text-xl font-bold text-zinc-900 dark:text-zinc-50 flex items-center gap-2">
                            <i class="fa fa-clipboard-check text-emerald-500"></i>
                            Informe de Mantención Preventiva
                        </h1>
                    </div>

                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
                        <div>
                            <p class="text-xs uppercase text-zinc-500 dark:text-zinc-400 font-semibold">Fecha</p>
                            <p class="text-zinc-900 dark:text-zinc-100">
                                {{ $informe->fecha->format('d/m/Y') }}
                            </p>
                        </div>

                        <div class="md:col-span-1 lg:col-span-2">
                            <p class="text-xs uppercase text-zinc-500 dark:text-zinc-400 font-semibold">
                                Centro Médico / Cliente
                            </p>
                            <p class="text-zinc-900 dark:text-zinc-100 font-medium">
                                {{ $informe->centroMedico->centro_dialisis }}
                            </p>
                        </div>

                        <div class="md:col-span-2 lg:col-span-2">
                            <p class="text-xs uppercase text-zinc-500 dark:text-zinc-400 font-semibold">Equipo</p>
                            <p class="text-zinc-900 dark:text-zinc-100">
                                {{ $informe->equipo->marca }} {{ $informe->equipo->modelo }} ·
                                SN: {{ $informe->equipo->numero_serie }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs uppercase text-zinc-500 dark:text-zinc-400 font-semibold">N° Inventario
                            </p>
                            <p class="text-zinc-900 dark:text-zinc-100">
                                {{ $informe->numero_inventario }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs uppercase text-zinc-500 dark:text-zinc-400 font-semibold">
                                Horas de Operación
                            </p>
                            <p class="text-zinc-900 dark:text-zinc-100">
                                {{ $informe->equipo->horas_uso }} h
                            </p>
                        </div>

                        <div>
                            <p class="text-xs uppercase text-zinc-500 dark:text-zinc-400 font-semibold">
                                Técnico Responsable
                            </p>
                            <p class="text-zinc-900 dark:text-zinc-100 font-medium">
                                {{ $informe->usuario->name }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Inspecciones --}}
                <div
                    class="bg-white dark:bg-zinc-900 shadow-sm rounded-lg border border-zinc-200 dark:border-zinc-700 p-6 mt-2">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-50 flex items-center gap-2">
                            <i class="fa fa-list-check text-indigo-500"></i>
                            Inspecciones Realizadas
                        </h2>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">
                            Total: {{ $informe->inspecciones->count() }} ítems
                        </span>
                    </div>

                    @if ($informe->inspecciones->isEmpty())
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">
                            No se registraron inspecciones en este informe.
                        </p>
                    @else
                        <div class="overflow-x-auto rounded-lg border border-zinc-200 dark:border-zinc-700">
                            <table class="min-w-full text-sm">
                                <thead class="bg-zinc-100 dark:bg-zinc-800">
                                    <tr>
                                        <th class="px-4 py-2 text-left font-semibold text-zinc-700 dark:text-white">
                                            #
                                        </th>
                                        <th class="px-4 py-2 text-left font-semibold text-zinc-700 dark:text-white">
                                            Descripción
                                        </th>
                                        <th class="px-4 py-2 text-left font-semibold text-zinc-700 dark:text-white">
                                            Respuesta
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($informe->inspecciones as $index => $inspeccion)
                                        @php
                                            $ans = $inspeccion->respuesta;
                                            $badgeClass = match ($ans) {
                                                'SI'
                                                    => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200',
                                                'NO' => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-200',
                                                'N/A' => 'bg-zinc-100 text-zinc-800 dark:bg-zinc-700 dark:text-white',
                                                default
                                                    => 'bg-zinc-100 text-zinc-800 dark:bg-zinc-700 dark:text-white',
                                            };
                                        @endphp
                                        <tr
                                            class="border-t border-zinc-200 dark:border-zinc-700 hover:bg-zinc-700 dark:hover:bg-zinc-800/60 transition">
                                            <td class="px-4 py-2 text-zinc-900 dark:text-white align-top">
                                                {{ $index + 1 }}
                                            </td>
                                            <td class="px-4 py-2 text-zinc-900 dark:text-white">
                                                {{ $inspeccion->descripcion }}
                                            </td>
                                            <td class="px-4 py-2">
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $badgeClass }}">
                                                    {{ $inspeccion->respuesta ?? '—' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                {{-- Comentarios + próximo control --}}
                <div class="grid md:grid-cols-2 gap-6 mt-2">
                    {{-- Comentarios --}}
                    <div
                        class="bg-white dark:bg-zinc-900 shadow-sm rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                        <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-50 mb-3 flex items-center gap-2">
                            <i class="fa fa-comment-dots text-sky-500"></i>
                            Comentarios
                        </h2>
                        <div
                            class="text-sm rounded-md border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 px-3 py-2 text-zinc-900 dark:text-white min-h-[60px] whitespace-pre-line">
                            {{ $informe->comentarios ?: 'Sin comentarios registrados.' }}
                        </div>
                    </div>

                    {{-- Próximo control --}}
                    <div
                        class="bg-white dark:bg-zinc-900 shadow-sm rounded-lg border border-zinc-200 dark:border-zinc-700 p-6 mt-2">
                        <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-50 mb-3 flex items-center gap-2">
                            <i class="fa fa-calendar-check text-pink-500"></i>
                            Próximo Control
                        </h2>
                        <div class="text-sm text-zinc-900 dark:text-white">
                            <p class="text-xs uppercase text-zinc-500 dark:text-zinc-400 font-semibold mb-1">
                                Fecha estimada
                            </p>
                            <p class="text-base">
                                @if ($informe->fecha_proximo_control)
                                    {{ $informe->fecha_proximo_control->format('d/m/Y') }}
                                @else
                                    <span class="text-zinc-500 dark:text-zinc-400">
                                        No especificada en este informe.
                                    </span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Firmas --}}
                <div
                    class="bg-white dark:bg-zinc-900 shadow-sm rounded-lg border border-zinc-200 dark:border-zinc-700 p-6 mt-6">

                    <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-50 mb-4 flex items-center gap-2">
                        <i class="fa fa-signature text-purple-500"></i>
                        Firmas
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">

                        {{-- Técnico Responsable --}}
                        <div class="flex flex-col gap-3">
                            <p class="text-sm font-semibold text-zinc-800 dark:text-white">
                                Técnico Responsable
                            </p>

                            <div class="border border-dashed border-zinc-400 dark:border-zinc-500 rounded-md bg-white dark:bg-zinc-950 
                        p-2 flex items-center justify-start"
                                style="width: 340px; height: 120px;">

                                @if ($informe->firma_tecnico)
                                    <img src="{{ $informe->firma_tecnico }}" alt="Firma Técnico" class="object-contain"
                                        style="max-width: 320px; max-height: 110px;">
                                @else
                                    <span class="text-xs text-zinc-400">Firma no disponible</span>
                                @endif
                            </div>

                            <p class="text-sm text-zinc-700 dark:text-zinc-300">
                                Nombre: <strong>{{ $informe->usuario->name }}</strong>
                            </p>
                        </div>

                        {{-- Cliente / Representante Legal --}}
                        <div class="flex flex-col gap-3">
                            <p class="text-sm font-semibold text-zinc-800 dark:text-white">
                                Cliente / Representante Legal
                            </p>

                            <div class="border border-dashed border-zinc-400 dark:border-zinc-500 rounded-md bg-white dark:bg-zinc-950 
                        p-2 flex items-center justify-start"
                                style="width: 340px; height: 120px;">

                                @if ($informe->firma_cliente)
                                    <img src="{{ $informe->firma_cliente }}" alt="Firma Cliente" class="object-contain"
                                        style="max-width: 320px; max-height: 110px;">
                                @else
                                    <span class="text-xs text-zinc-400">Firma no disponible</span>
                                @endif
                            </div>

                            <p class="text-sm text-zinc-500 dark:text-zinc-400 text-left">
                                Nombre y firma del representante del centro de diálisis.
                            </p>
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
                        <a href="{{ route('informes.download', ['tipo' => 'preventivo', 'id' => $informe->id]) }}"
                            class="inline-flex items-center px-4 py-2 rounded-lg bg-red-500 hover:bg-red-700
                              text-white text-sm font-medium transition">
                            <i class="fa fa-file-pdf mr-2"></i>
                            Descargar PDF
                        </a>

                        <button type="button"
                            onclick="const win = window.open('{{ route('informes.print', ['tipo' => 'preventivo', 'id' => $informe->id]) }}', '_blank'); if (win) { win.focus(); win.print(); }"
                            class="inline-flex items-center px-4 py-2 rounded-lg bg-blue-500 hover:bg-blue-700
                                   text-white text-sm font-medium transition">
                            <i class="fa fa-print mr-2"></i>
                            Imprimir
                        </button>
                    </div>
                </div>
            </div>
            </x-app-layout>
