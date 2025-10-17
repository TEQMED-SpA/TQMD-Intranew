<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detalle del Rol') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h1 class="text-3xl font-bold">{{ $rol->nombre }}</h1>
                            <p class="text-gray-600 dark:text-gray-400 mt-2">
                                {{ $rol->descripcion ?? __('Sin descripción') }}</p>
                        </div>
                        <div class="text-right">
                            @if (isset($rol->activo))
                                @if ($rol->activo)
                                    <span
                                        class="px-3 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-full text-sm font-semibold">
                                        <i class="fas fa-check-circle"></i> {{ __('Activo') }}
                                    </span>
                                @else
                                    <span
                                        class="px-3 py-1 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 rounded-full text-sm font-semibold">
                                        <i class="fas fa-times-circle"></i> {{ __('Inactivo') }}
                                    </span>
                                @endif
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="bg-blue-50 dark:bg-blue-900 p-4 rounded-lg">
                            <div class="flex items-center">
                                <div class="p-3 bg-blue-500 rounded-full">
                                    <i class="fas fa-users text-white text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Usuarios Asignados') }}
                                    </p>
                                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                        {{ $rol->usuarios_count }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-purple-50 dark:bg-purple-900 p-4 rounded-lg">
                            <div class="flex items-center">
                                <div class="p-3 bg-purple-500 rounded-full">
                                    <i class="fas fa-shield-alt text-white text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ __('Privilegios Asignados') }}</p>
                                    <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                                        {{ $rol->privilegios->count() }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-green-50 dark:bg-green-900 p-4 rounded-lg">
                            <div class="flex items-center">
                                <div class="p-3 bg-green-500 rounded-full">
                                    <i class="fas fa-calendar-alt text-white text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Creado') }}</p>
                                    <p class="text-lg font-bold text-green-600 dark:text-green-400">
                                        {{ $rol->created_at ? $rol->created_at->format('d/m/Y') : '-' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <h2 class="text-2xl font-bold mb-4">
                            <i class="fas fa-list-check"></i> {{ __('Privilegios Asignados') }}
                        </h2>

                        @if ($privilegiosPorModulo->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach ($privilegiosPorModulo as $modulo => $privs)
                                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                        <h3 class="font-semibold text-lg mb-3 text-indigo-600 dark:text-indigo-400">
                                            <i class="fas fa-folder"></i> {{ ucfirst($modulo) }}
                                        </h3>
                                        <ul class="space-y-2">
                                            @foreach ($privs as $privilegio)
                                                <li class="flex items-center text-sm">
                                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                                    <span>{{ $privilegio->nombre }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div
                                class="bg-yellow-50 dark:bg-yellow-900 border border-yellow-200 dark:border-yellow-700 p-4 rounded-lg">
                                <p class="text-yellow-800 dark:text-yellow-200">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    {{ __('Este rol no tiene privilegios asignados.') }}
                                </p>
                            </div>
                        @endif
                    </div>

                    @if ($rol->users->count() > 0)
                        <div class="mb-6">
                            <h2 class="text-2xl font-bold mb-4">
                                <i class="fas fa-user-friends"></i> {{ __('Usuarios con este Rol') }}
                            </h2>
                            <div class="overflow-x-auto">
                                <table class="w-full border border-gray-300 dark:border-gray-700">
                                    <thead class="bg-gray-200 dark:bg-gray-700">
                                        <tr>
                                            <th class="border border-gray-300 dark:border-gray-600 p-2 text-left">
                                                {{ __('Nombre') }}</th>
                                            <th class="border border-gray-300 dark:border-gray-600 p-2 text-left">
                                                {{ __('Correo') }}</th>
                                            <th class="border border-gray-300 dark:border-gray-600 p-2 text-center">
                                                {{ __('Estado') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($rol->users as $usuario)
                                            <tr class="border border-gray-300 dark:border-gray-700">
                                                <td class="p-2">{{ $usuario->name ?? $usuario->nomape }}</td>
                                                <td class="p-2">{{ $usuario->email ?? $usuario->correo }}</td>
                                                <td class="p-2 text-center">
                                                    @if (isset($usuario->activo) && $usuario->activo)
                                                        <span
                                                            class="px-2 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-full text-xs">
                                                            {{ __('Activo') }}
                                                        </span>
                                                    @elseif(isset($usuario->activo))
                                                        <span
                                                            class="px-2 py-1 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 rounded-full text-xs">
                                                            {{ __('Inactivo') }}
                                                        </span>
                                                    @else
                                                        <span class="text-gray-500">—</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <div class="flex justify-between mt-6">
                        <a href="{{ route('roles.index') }}"
                            class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg">
                            <i class="fas fa-arrow-left"></i> {{ __('Volver') }}
                        </a>

                        @role('admin')
                            <div class="flex space-x-2">
                                <a href="{{ route('roles.edit', $rol->id) }}"
                                    class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white font-semibold rounded-lg">
                                    <i class="fas fa-edit"></i> {{ __('Editar') }}
                                </a>
                                @if ($rol->usuarios_count == 0 && $rol->nombre !== 'admin')
                                    <form action="{{ route('roles.destroy', $rol->id) }}" method="POST"
                                        onsubmit="return confirm('{{ __('¿Está seguro de eliminar este rol?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg">
                                            <i class="fas fa-trash"></i> {{ __('Eliminar') }}
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endrole
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
