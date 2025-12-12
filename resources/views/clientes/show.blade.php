<x-layouts.app :title="'Cliente: ' . ($cliente->nombre ?? '—')">
    <div class="min-h-screen px-4 py-8 bg-zinc-50 dark:bg-zinc-900 transition-all">
        <div class="max-w-6xl mx-auto space-y-6">
            {{-- Header --}}
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <p class="text-sm uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Clientes</p>
                    <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">
                        {{ $cliente->nombre ?? 'Cliente' }}
                    </h1>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">
                        RUT: {{ $cliente->rut_formateado ?? ($cliente->rut ?? '—') }}
                    </p>
                </div>
                <div class="flex flex-wrap gap-2">
                    @role('admin|auditor')
                        <a href="{{ route('clientes.edit', $cliente) }}"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-green-600 hover:bg-green-700 text-white text-sm font-semibold transition">
                            <i class="fa fa-pen"></i>
                            Editar
                        </a>
                    @endrole
                    <a href="{{ route('clientes.index') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-zinc-300 dark:border-zinc-700 text-sm font-semibold text-zinc-700 dark:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition">
                        <i class="fa fa-arrow-left text-xs"></i>
                        Volver
                    </a>
                </div>
            </div>

            {{-- Resumen --}}
            @php
                $centrosCount = $cliente->centros_medicos->count();
            @endphp
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-4">
                    <p class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Contacto</p>
                    <p class="mt-2 text-lg font-semibold text-zinc-900 dark:text-white">
                        <i class="fa fa-phone"></i> {{ $cliente->telefono ?? 'No registrado' }}
                    </p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">
                        <i class="fa fa-envelope"></i> {{ $cliente->email ?? 'No registrado' }}

                    </p>
                </div>
                <div class="rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-4">
                    <p class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Centros asociados</p>
                    <p class="mt-2 text-3xl font-bold text-zinc-900 dark:text-white">
                        {{ number_format($centrosCount) }}
                    </p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Registrados para este cliente</p>
                </div>
                <div class="rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-4">
                    <p class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Razón social</p>
                    <p class="mt-2 text-lg font-semibold text-zinc-900 dark:text-white">
                        {{ $cliente->razon_social ?? '—' }}
                    </p>
                </div>
            </div>

            {{-- Información del cliente --}}
            <div
                class="rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-sm p-6">
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4">Detalles del cliente</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-zinc-500 dark:text-zinc-400">Nombre</p>
                        <p class="font-medium text-zinc-900 dark:text-zinc-100">
                            {{ $cliente->nombre ?? '—' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-zinc-500 dark:text-zinc-400">Correo electrónico</p>
                        <p class="font-medium text-zinc-900 dark:text-zinc-100">
                            {{ $cliente->email ?? '—' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-zinc-500 dark:text-zinc-400">Teléfono</p>
                        <p class="font-medium text-zinc-900 dark:text-zinc-100">
                            {{ $cliente->telefono ?? '—' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-zinc-500 dark:text-zinc-400">RUT</p>
                        <p class="font-medium text-zinc-900 dark:text-zinc-100">
                            {{ $cliente->rut_formateado ?? ($cliente->rut ?? '—') }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Centros asociados --}}
            <div class="rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-sm">
                <div class="p-6 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">Centros médicos</h2>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">
                            Listado de centros registrados para este cliente.
                        </p>
                    </div>
                    <a href="{{ route('centros_medicos.create') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold transition">
                        <i class="fa fa-plus text-xs"></i>
                        Nuevo centro
                    </a>
                </div>

                <div class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    @forelse ($cliente->centros_medicos as $centro)
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2 px-6 py-4">
                            <div>
                                <p class="text-base font-semibold text-zinc-900 dark:text-white">
                                    {{ $centro->nombre ?? ($centro->centro_dialisis ?? 'Centro sin nombre') }}
                                </p>
                                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                                    {{ $centro->direccion ?? 'Dirección no registrada' }} ·
                                    {{ $centro->region ?? 'Región desconocida' }}
                                </p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                    {{ $centro->activo ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-200' : 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-200' }}">
                                    {{ $centro->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                                <a href="{{ route('centros_medicos.show', $centro) }}"
                                    class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-zinc-300 dark:border-zinc-700 text-sm font-semibold text-zinc-700 dark:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition">
                                    Ver centro
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-10 text-center text-zinc-500 dark:text-zinc-400">
                            Este cliente aún no tiene centros registrados.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
