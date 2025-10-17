<x-layouts.app>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-zinc-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-zinc-900 dark:text-zinc-100">
                    <div class="mb-6">
                        <h2 class="font-semibold text-xl text-zinc-800 dark:text-zinc-200 leading-tight">
                            {{ __('Gestión de Roles') }}
                        </h2>
                    </div>

                    {{-- Mensajes --}}
                    @if (session('status'))
                        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                            <i class="fas fa-check-circle"></i> {{ session('status') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                        </div>
                    @endif

                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-bold">{{ __('Listado de Roles') }}</h1>

                        @role('admin')
                            <a href="{{ route('roles.create') }}"
                                class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg">
                                <i class="fas fa-plus"></i> {{ __('Nuevo Rol') }}
                            </a>
                        @endrole
                    </div>

                    {{-- Tabla: ID | Nombre | Descripción | Usuarios (cantidad) | Permisos (cantidad) | Estado | Acciones --}}
                    <div class="overflow-x-auto">
                        <table class="w-full border border-zinc-300 dark:border-zinc-700">
                            <thead class="bg-zinc-200 dark:bg-zinc-700">
                                <tr>
                                    <th class="border border-zinc-300 dark:border-zinc-600 p-2 text-left">ID</th>
                                    <th class="border border-zinc-300 dark:border-zinc-600 p-2 text-left">
                                        {{ __('Nombre') }}</th>
                                    <th class="border border-zinc-300 dark:border-zinc-600 p-2 text-left">
                                        {{ __('Descripción') }}</th>
                                    <th class="border border-zinc-300 dark:border-zinc-600 p-2 text-center">
                                        {{ __('Usuarios (cantidad)') }}</th>
                                    <th class="border border-zinc-300 dark:border-zinc-600 p-2 text-center">
                                        {{ __('Permisos (cantidad)') }}</th>
                                    <th class="border border-zinc-300 dark:border-zinc-600 p-2 text-center">
                                        {{ __('Estado') }}</th>
                                    <th class="border border-zinc-300 dark:border-zinc-600 p-2 text-center">
                                        {{ __('Acciones') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($roles as $rol)
                                    <tr class="border border-zinc-300 dark:border-zinc-700">
                                        <td class="p-2">{{ $rol->id }}</td>

                                        <td class="p-2 font-semibold">{{ $rol->nombre }}</td>

                                        <td class="p-2">
                                            {{ $rol->descripcion ?? '-' }}
                                        </td>

                                        <td class="p-2 text-center">
                                            <span
                                                class="px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded-full text-sm">
                                                {{ $rol->usuarios_count ?? $rol->users()->count() }}
                                            </span>
                                        </td>

                                        <td class="p-2 text-center">
                                            <span
                                                class="px-2 py-1 bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200 rounded-full text-sm">
                                                {{ $rol->permisos_count ?? $rol->privilegios->count() }}
                                            </span>
                                        </td>

                                        <td class="p-2 text-center">
                                            @if (!is_null($rol->activo ?? null))
                                                @if ($rol->activo)
                                                    <span
                                                        class="px-2 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-full text-sm">
                                                        {{ __('Activo') }}
                                                    </span>
                                                @else
                                                    <span
                                                        class="px-2 py-1 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 rounded-full text-sm">
                                                        {{ __('Inactivo') }}
                                                    </span>
                                                @endif
                                            @else
                                                <span class="text-zinc-500">—</span>
                                            @endif
                                        </td>

                                        <td class="p-2">
                                            <div class="flex justify-center gap-2">
                                                <a href="{{ route('roles.edit', $rol) }}"
                                                    class="px-3 py-1 bg-yellow-600 hover:bg-yellow-700 text-white rounded-md text-sm">
                                                    <i class="fas fa-edit"></i> {{ __('Editar') }}
                                                </a>

                                                <form action="{{ route('roles.destroy', $rol) }}" method="POST"
                                                    onsubmit="return confirm('{{ __('¿Está seguro de eliminar este rol?') }}')"
                                                    class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded-md text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                                                        @if (($rol->usuarios_count ?? $rol->users()->count()) > 0 || $rol->nombre === 'admin') disabled @endif>
                                                        <i class="fas fa-trash"></i> {{ __('Eliminar') }}
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center p-4 text-zinc-500">
                                            {{ __('No se encontraron roles') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
