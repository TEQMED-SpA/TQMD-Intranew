<x-layouts.app :title="$title ?? 'Editar Rol'">
    <div class="min-h-screen px-4 py-8 bg-zinc-50 dark:bg-zinc-800 transition-all">
        <div class="max-w-4xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-zinc-800 dark:text-white">Editar Rol</h1>
                <a href="{{ route('roles.index') }}">
                    <button type="button"
                        class="bg-zinc-200 dark:bg-zinc-500 text-zinc-800 dark:text-zinc-100 font-semibold px-4 py-2 rounded-lg hover:bg-zinc-300 dark:hover:bg-zinc-600 transition flex items-center gap-2">
                        <i class="fa fa-arrow-left"></i>
                        Volver
                    </button>
                </a>
            </div>
            <div class="rounded-lg shadow bg-white dark:bg-zinc-900 p-6">
                @include('admin.roles.partials.form', [
                    'rol' => $rol,
                    'privilegiosPorModulo' => $privilegiosPorModulo,
                    'privilegiosAsignados' => $privilegiosAsignados,
                ])

            </div>
        </div>
    </div>
    </div>
    </x-app-layout>
