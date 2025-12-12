<x-layouts.app :title="'Nuevo informe preventivo'">
    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div
                class="bg-white dark:bg-zinc-900 shadow-sm rounded-xl border border-zinc-200 dark:border-zinc-800 p-6 space-y-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                            <i class="fa fa-clipboard-list text-emerald-500"></i>
                            Selecciona el tipo de informe preventivo
                        </h1>
                        <p class="text-sm text-zinc-600 dark:text-zinc-300 mt-1">
                            Elige el protocolo que necesitas completar para continuar al formulario correspondiente.
                        </p>
                    </div>
                    <a href="{{ route('informes.index') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-200 rounded-lg hover:bg-zinc-200 dark:hover:bg-zinc-700 transition">
                        <i class="fa fa-arrow-left text-xs"></i>
                        Volver a Informes
                    </a>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    @forelse ($tipos as $tipo)
                        <div
                            class="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/40 p-5 flex flex-col gap-3">
                            <div class="flex items-center gap-3">
                                <span
                                    class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 dark:bg-emerald-900/40 dark:text-emerald-200 text-lg font-semibold">
                                    {{ mb_substr($tipo->nombre, 0, 2) }}
                                </span>
                                <div>
                                    <p class="text-base font-semibold text-zinc-900 dark:text-white">
                                        {{ $tipo->nombre }}
                                    </p>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400">
                                        {{ $tipo->activo ? 'Disponible' : 'No disponible' }}
                                    </p>
                                </div>
                            </div>
                            <p class="text-sm text-zinc-600 dark:text-zinc-300">
                                Completa el formulario específico para este protocolo preventivo.
                            </p>
                            <div class="flex justify-end">
                                <a href="{{ route('informes.preventivos.create', $tipo) }}"
                                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition">
                                    <i class="fa fa-file-signature text-xs"></i>
                                    Completar informe
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-10 text-zinc-600 dark:text-zinc-400">
                            No hay tipos de informe preventivo activos.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
