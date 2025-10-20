<form action="{{ route('roles.update', $rol) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="mb-6 p-4 bg-zinc-50 dark:bg-zinc-800 rounded-lg">
        <div class="mb-4">
            <div class="mb-4 pb-4 border-b border-zinc-200 dark:border-zinc-700">
                <x-input-label for="nombre" :value="__('Nombre del Rol')" />
                <input id="nombre" name="nombre" type="text"
                    class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300
                focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600
                rounded-md shadow-sm"
                    value="{{ old('nombre', $rol->nombre) }}" required>
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
                    <x-input-error :messages="$errors->get('activo')" class="mt-2" />
                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Rol Activo') }}</span>
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
                                    <input type="checkbox" name="privilegios[]" value="{{ $privilegio->id }}"
                                        {{ in_array($privilegio->id, old('privilegios', $privilegiosAsignados ?? [])) ? 'checked' : '' }}
                                        class="privilegio-checkbox rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    <x-input-error :messages="$errors->get('privilegios')" class="mt-2" />
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
                    class="bg-zinc-200 dark:bg-zinc-700 hover:bg-zinc-300 dark:hover:bg-zinc-600 text-zinc-800 dark:text-white font-semibold px-6 py-2 rounded-lg transition">
                    <i class="fas fa-arrow-left"></i> {{ __('Cancelar') }}
                </a>
                <button type="submit"
                    class="bg-zinc-600 hover:bg-zinc-700 text-white font-semibold px-6 py-2 rounded-lg transition">
                    <i class="fas fa-save"></i> {{ __('Actualizar Rol') }}
                </button>
            </div>
</form>

<script>
    function selectAll() {
        document.querySelectorAll('.privilegio-checkbox').forEach(cb => cb.checked = true);
    }

    function deselectAll() {
        document.querySelectorAll('.privilegio-checkbox').forEach(cb => cb.checked = false);
    }
</script>
