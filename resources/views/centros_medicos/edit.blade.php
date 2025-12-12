@php($centros_medico = $centroMedico)

<x-layouts.app :title="'Editar centro: ' . ($centros_medico->nombre ?? 'Centro médico')">
    <div class="min-h-screen px-4 py-8 bg-zinc-50 dark:bg-zinc-900 transition-all">
        <div class="max-w-3xl mx-auto space-y-6">
            {{-- Header --}}
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <p class="text-sm uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Centros médicos</p>
                    <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">
                        {{ $centros_medico->nombre ?? 'Centro médico' }}
                    </h1>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">
                        Actualiza la información del centro y los datos de contacto.
                    </p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('centros_medicos.show', $centroMedico) }}"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-zinc-300 dark:border-zinc-700 text-sm font-semibold text-zinc-700 dark:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition">
                        <i class="fa fa-eye text-xs"></i>
                        Ver ficha
                    </a>
                    <a href="{{ route('centros_medicos.index') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-zinc-200 dark:bg-zinc-700 hover:bg-zinc-300 dark:hover:bg-zinc-600 text-sm font-semibold text-zinc-800 dark:text-white transition">
                        <i class="fa fa-arrow-left text-xs"></i>
                        Volver
                    </a>
                </div>
            </div>

            {{-- Formulario --}}
            <div
                class="rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-sm p-6">
                <form action="{{ route('centros_medicos.update', $centroMedico) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    {{-- Cliente y estado --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="cliente_id"
                                class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200 mb-1">Cliente</label>
                            <select name="cliente_id" id="cliente_id"
                                class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-sm text-zinc-900 dark:text-zinc-100 focus:ring focus:border-blue-500">
                                <option value="">-- Sin asignar --</option>
                                @foreach ($clientes as $cliente)
                                    <option value="{{ $cliente->id }}" @selected(old('cliente_id', $centros_medico->cliente_id) == $cliente->id)>
                                        {{ $cliente->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('cliente_id')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200 mb-1">
                                Estado
                            </label>
                            <div
                                class="flex items-center justify-between rounded-lg border border-zinc-200 dark:border-zinc-700 px-4 py-3 bg-zinc-50 dark:bg-zinc-800">
                                <span class="text-sm text-zinc-600 dark:text-zinc-300">
                                    {{ old('activo', $centros_medico->activo) ? 'Centro activo' : 'Centro inactivo' }}
                                </span>
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="hidden" name="activo" value="0">
                                    <input type="checkbox" name="activo" value="1" class="sr-only peer"
                                        @checked(old('activo', $centros_medico->activo))>
                                    <div
                                        class="w-11 h-6 bg-zinc-300 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 rounded-full peer dark:bg-zinc-700 peer-checked:bg-green-500 relative transition">
                                        <div
                                            class="absolute top-1 left-1 h-4 w-4 rounded-full bg-white transition peer-checked:translate-x-5">
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Nombre --}}
                    <div>
                        <label for="nombre"
                            class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200 mb-1">Nombre del
                            centro</label>
                        <input type="text" id="nombre" name="nombre"
                            value="{{ old('nombre', $centros_medico->nombre) }}"
                            class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-sm text-zinc-900 dark:text-zinc-100 px-3 py-2 focus:ring focus:border-blue-500"
                            required>
                        @error('nombre')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Dirección --}}
                    <div>
                        <label for="direccion"
                            class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200 mb-1">Dirección</label>
                        <input type="text" id="direccion" name="direccion"
                            value="{{ old('direccion', $centros_medico->direccion) }}"
                            class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-sm text-zinc-900 dark:text-zinc-100 px-3 py-2 focus:ring focus:border-blue-500">
                        @error('direccion')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Ciudad y Región --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="ciudad"
                                class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200 mb-1">Ciudad</label>
                            <input type="text" id="ciudad" name="ciudad"
                                value="{{ old('ciudad', $centros_medico->ciudad) }}"
                                class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-sm text-zinc-900 dark:text-zinc-100 px-3 py-2 focus:ring focus:border-blue-500">
                            @error('ciudad')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="region"
                                class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200 mb-1">Región</label>
                            <input type="text" id="region" name="region"
                                value="{{ old('region', $centros_medico->region) }}"
                                class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-sm text-zinc-900 dark:text-zinc-100 px-3 py-2 focus:ring focus:border-blue-500">
                            @error('region')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Teléfono --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="telefono"
                                class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200 mb-1">Teléfono</label>
                            <input type="text" id="telefono" name="telefono"
                                value="{{ old('telefono', $centros_medico->telefono) }}"
                                class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-sm text-zinc-900 dark:text-zinc-100 px-3 py-2 focus:ring focus:border-blue-500">
                            @error('telefono')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200 mb-1">
                                Códigos internos
                            </label>
                        </div>
                    </div>

                    {{-- Botones --}}
                    <div class="flex flex-col sm:flex-row sm:justify-end gap-3">
                        <a href="{{ route('centros_medicos.index') }}"
                            class="inline-flex justify-center items-center gap-2 px-4 py-2 rounded-lg border border-zinc-300 dark:border-zinc-700 text-sm font-semibold text-zinc-700 dark:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800">
                            Cancelar
                        </a>
                        <button type="submit"
                            class="inline-flex justify-center items-center gap-2 px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold transition">
                            <i class="fa fa-check text-xs"></i>
                            Guardar cambios
                        </button>
                    </div>
                </form>
            </div>

            {{-- Metadatos --}}
            <div class="text-xs text-zinc-500 dark:text-zinc-400 text-center">
                Última actualización: {{ optional($centros_medico->updated_at)->format('d-m-Y H:i') ?? '—' }}
            </div>
        </div>
    </div>
</x-layouts.app>
