<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Editar Rol') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h1 class="text-2xl font-bold mb-4">{{ __('Editar Rol:') }} {{ $rol->nombre }}</h1>

                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                            <strong>{{ __('Errores en el formulario:') }}</strong>
                            <ul class="mt-2 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif>

                    <form action="{{ route('roles.update', $rol->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <x-input-label for="nombre" :value="__('Nombre del Rol')" />
                            <x-text-input id="nombre" class="block mt-1 w-full" type="text" name="nombre"
                                :value="old('nombre', $rol->nombre)" required />
                            <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="descripcion" :value="__('Descripción')" />
                            <textarea id="descripcion" name="descripcion" rows="3"
                                class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('descripcion', $rol->descripcion ?? '') }}</textarea>
                            <x-input-error :messages="$errors->get('descripcion')" class="mt-2" />
                        </div>

                        <div class="mb-6">
                            <label class="flex items-center">
                                <input type="checkbox" name="activo" value="1"
                                    {{ old('activo', $rol->activo ?? false) ? 'checked' : '' }}
                                    class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                <span
                                    class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Rol Activo') }}</span>
                            </label>
                        </div>

                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-3">{{ __('Asignar Privilegios') }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                {{ __('Selecciona los privilegios que tendrá este rol') }}
                            </p>

                            <div class="mb-3 flex space-x-2">
                                <button type="button" onclick="selectAll()"
                                    class="px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white text-sm rounded">
                                    {{ __('Seleccionar Todos') }}
                                </button>
                                <button type="button" onclick="deselectAll()"
                                    class="px-3 py-1 bg-gray-500 hover:bg-gray-600 text-white text-sm rounded">
                                    {{ __('Deseleccionar Todos') }}
                                </button>
                            </div>

                            @foreach ($privilegiosPorModulo as $modulo => $lista)
                                <div class="mb-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <h4 class="font-semibold mb-2 text-indigo-600 dark:text-indigo-400">
                                        <i class="fas fa-folder"></i> {{ ucfirst($modulo) }}
                                    </h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                        @foreach ($lista as $privilegio)
                                            <label
                                                class="flex items-center p-2 hover:bg-gray-100 dark:hover:bg-gray-600 rounded cursor-pointer">
                                                <input type="checkbox" name="privilegios[]"
                                                    value="{{ $privilegio->id }}"
                                                    {{ in_array($privilegio->id, old('privilegios', $privilegiosAsignados)) ? 'checked' : '' }}
                                                    class="privilegio-checkbox rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                                <span class="ml-2 text-sm">{{ $privilegio->nombre }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900 rounded-lg">
                            <p class="text-sm text-blue-800 dark:text-blue-200">
                                <i class="fas fa-info-circle"></i>
                                <strong>{{ __('Nota:') }}</strong>
                                {{ __('Este rol actualmente tiene') }}
                                <strong>{{ $rol->usuarios_count ?? 0 }}
                                    {{ Str::plural('usuario', $rol->usuarios_count ?? 0) }}</strong>
                                {{ __('asignado(s).') }}
                            </p>
                        </div>

                        <div class="flex justify-between mt-6">
                            <a href="{{ route('roles.index') }}"
                                class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg">
                                <i class="fas fa-arrow-left"></i> {{ __('Cancelar') }}
                            </a>
                            <button type="submit"
                                class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white font-semibold rounded-lg">
                                <i class="fas fa-save"></i> {{ __('Actualizar Rol') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function selectAll() {
            document.querySelectorAll('.privilegio-checkbox').forEach(cb => cb.checked = true);
        }

        function deselectAll() {
            document.querySelectorAll('.privilegio-checkbox').forEach(cb => cb.checked = false);
        }
    </script>
</x-app-layout>
