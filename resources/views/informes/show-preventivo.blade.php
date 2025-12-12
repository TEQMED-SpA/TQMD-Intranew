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

                        <div>
                            <p class="text-xs uppercase text-zinc-500 dark:text-zinc-400 font-semibold">Cliente</p>
                            <p class="text-zinc-900 dark:text-zinc-100 font-medium">
                                {{ optional($informe->centroMedico->cliente)->nombre ?? '—' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs uppercase text-zinc-500 dark:text-zinc-400 font-semibold">Centro Médico
                            </p>
                            <p class="text-zinc-900 dark:text-zinc-100 font-medium">
                                {{ $informe->centroMedico->centro_dialisis ?? ($informe->centroMedico->nombre ?? '—') }}
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
                            <p class="text-xs uppercase text-zinc-500 dark:text-zinc-400 font-semibold">ID/N° Inventario
                            </p>
                            <p class="text-zinc-900 dark:text-zinc-100">
                                {{ $informe->equipo->codigo ?? ($informe->numero_inventario ?? '—') }}
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
                        @php
                            $sections = collect($seccionesInspeccion ?? []);
                            if ($sections->isEmpty()) {
                                $sections = collect([
                                    [
                                        'title' => null,
                                        'items' => $informe->inspecciones
                                            ->map(function ($inspeccion, $index) {
                                                $codigo = null;
                                                $titulo = $inspeccion->descripcion;
                                                if (
                                                    preg_match(
                                                        '/^([0-9]+(?:\\.[0-9]+)*)\\s+(.*)$/u',
                                                        $inspeccion->descripcion,
                                                        $matches,
                                                    )
                                                ) {
                                                    $codigo = $matches[1];
                                                    $titulo = $matches[2];
                                                }

                                                $comentario = $inspeccion->comentario;
                                                $commentText = $comentario;
                                                $commentDetails = [];
                                                $decoded = json_decode($comentario ?? '', true);
                                                if (
                                                    is_array($decoded) &&
                                                    !empty($decoded['__structured']) &&
                                                    is_array($decoded['values'] ?? null)
                                                ) {
                                                    $commentText = null;
                                                    foreach ($decoded['values'] as $key => $value) {
                                                        $val = trim((string) $value);
                                                        if ($val === '') {
                                                            continue;
                                                        }
                                                        $commentDetails[] = [
                                                            'label' => ucwords(str_replace('_', ' ', (string) $key)),
                                                            'value' => $val,
                                                            'suffix' => '',
                                                        ];
                                                    }
                                                }

                                                return [
                                                    'codigo' => $codigo ?? (string) ($index + 1),
                                                    'titulo' => $titulo,
                                                    'respuesta' => $inspeccion->respuesta,
                                                    'comment_text' => $commentText ? trim($commentText) : null,
                                                    'comment_details' => $commentDetails,
                                                ];
                                            })
                                            ->toArray(),
                                    ],
                                ]);
                            }
                        @endphp
                        <div class="overflow-x-auto rounded-lg border border-zinc-200 dark:border-zinc-700">
                            <table class="min-w-full text-sm">
                                <thead class="bg-zinc-100 dark:bg-zinc-800">
                                    <tr>
                                        <th class="px-4 py-2 text-left font-semibold text-zinc-700 dark:text-white">
                                            ID
                                        </th>
                                        <th class="px-4 py-2 text-left font-semibold text-zinc-700 dark:text-white">
                                            Ítem
                                        </th>
                                        <th class="px-4 py-2 text-left font-semibold text-zinc-700 dark:text-white">
                                            Respuesta
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sections as $section)
                                        @if ($section['title'])
                                            <tr class=" text-zinc-600 dark:text-zinc-200">
                                                <td colspan="3"
                                                    class="px-4 py-2 font-semibold text-zinc-600 dark:text-zinc-200">
                                                    {{ $section['title'] }}
                                                </td>
                                            </tr>
                                        @endif
                                        @foreach ($section['items'] as $item)
                                            @php
                                                $ans = $item['respuesta'];
                                                $badgeClass = match ($ans) {
                                                    'SI'
                                                        => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200',
                                                    'NO'
                                                        => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-200',
                                                    'N/A'
                                                        => 'bg-zinc-100 text-zinc-800 dark:bg-zinc-700 dark:text-white',
                                                    default
                                                        => 'bg-zinc-100 text-zinc-800 dark:bg-zinc-700 dark:text-white',
                                                };
                                                $commentText = $item['comment_text'] ?? ($item['comentario'] ?? null);
                                                $commentDetails = $item['comment_details'] ?? [];
                                            @endphp
                                            <tr
                                                class="border-t border-zinc-200 dark:border-zinc-700 hover:bg-zinc-300 dark:hover:bg-zinc-700/60 transition">
                                                <td
                                                    class="px-4 py-2 text-zinc-900 dark:text-white align-top font-semibold">
                                                    {{ $item['codigo'] }}
                                                </td>
                                                <td class="px-4 py-2 text-zinc-900 dark:text-white">
                                                    <div class="flex flex-col gap-1">
                                                        <span>{{ $item['titulo'] }}</span>
                                                        @if ($commentText)
                                                            <span
                                                                class="inline-flex items-center gap-2 text-xs text-zinc-600 dark:text-zinc-300">
                                                                <i class="fa fa-pen text-indigo-400"></i>
                                                                <strong>{{ $commentText }}</strong>
                                                            </span>
                                                        @endif
                                                        @if (!empty($commentDetails))
                                                            <div
                                                                class="flex flex-col gap-1 text-xs text-zinc-600 dark:text-zinc-300">
                                                                @foreach ($commentDetails as $detail)
                                                                    <span class="inline-flex items-center gap-2">
                                                                        <i class="fa fa-pen text-indigo-400"></i>
                                                                        {{ $detail['label'] ?? 'Valor' }}:
                                                                        <strong>{{ $detail['value'] ?? '—' }}{{ $detail['suffix'] ?? '' }}</strong>
                                                                    </span>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="px-4 py-2">
                                                    <span
                                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $badgeClass }}">
                                                        {{ $item['respuesta'] ?? '—' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
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

                            <div class="border border-dashed border-zinc-400 dark:border-zinc-600 rounded-md bg-white dark:bg-zinc-950/60
                        p-2 flex flex-col gap-2 items-center justify-center transition"
                                style="width: 340px; min-height: 140px;">

                                @if ($informe->firma_tecnico)
                                    <span class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">
                                        Firma técnico
                                    </span>
                                    <div
                                        class="w-full h-28 bg-white dark:bg-white rounded-md shadow-inner flex items-center justify-center px-2">
                                        <img src="{{ $informe->firma_tecnico }}" alt="Firma Técnico"
                                            class="object-contain max-h-24">
                                    </div>
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

                            <div class="border border-dashed border-zinc-400 dark:border-zinc-600 rounded-md bg-white dark:bg-zinc-950/60 
                        p-2 flex flex-col gap-2 items-center justify-center transition"
                                style="width: 340px; min-height: 140px;">

                                @if ($informe->firma_cliente)
                                    <span class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">
                                        Firma cliente
                                    </span>
                                    <div
                                        class="w-full h-28 bg-white dark:bg-white rounded-md shadow-inner flex items-center justify-center px-2">
                                        <img src="{{ $informe->firma_cliente }}" alt="Firma Cliente"
                                            class="object-contain max-h-24">
                                    </div>
                                @else
                                    <span class="text-xs text-zinc-400">Firma no disponible</span>
                                @endif
                            </div>

                            @if ($informe->firma_cliente_nombre)
                                <p class="text-sm text-zinc-700 dark:text-zinc-300">
                                    Nombre: <strong>{{ $informe->firma_cliente_nombre }}</strong>
                                </p>
                            @else
                                <p class="text-sm text-zinc-500 dark:text-zinc-400 text-left">
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
