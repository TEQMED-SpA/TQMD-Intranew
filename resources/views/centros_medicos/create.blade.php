<x-layouts.app :title="$title ?? 'Nuevo Centro Médico'">
    <div class="min-h-screen px-4 py-8 bg-zinc-50 dark:bg-zinc-800 transition-all">
        <div class="max-w-2xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-zinc-800 dark:text-white">Nuevo Centro Médico</h1>
                <a href="{{ route('centros_medicos.index') }}">
                    <button type="button"
                        class="bg-zinc-200 dark:bg-zinc-700 text-zinc-100 dark:text-zinc-100 font-semibold px-4 py-2 rounded-lg hover:bg-zinc-300 dark:hover:bg-zinc-600 transition flex items-center gap-2">
                        <i class="fa fa-arrow-left"></i>
                        Volver
                    </button>
                </a>
            </div>

            <div class="rounded-lg shadow bg-white dark:bg-zinc-900 p-6">
                <form action="{{ route('centros_medicos.store') }}" method="POST">
                    @csrf

                    {{-- Cliente --}}
                    <div class="mb-4">
                        <label for="cliente_id"
                            class="block text-zinc-700 dark:text-zinc-200 font-semibold mb-1">Cliente</label>
                        <select name="cliente_id" id="cliente_id"
                            class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring focus:border-blue-400">
                            <option value="">-- Sin asignar --</option>
                            @foreach ($clientes as $cliente)
                                <option value="{{ $cliente->id }}" @selected(old('cliente_id') == $cliente->id)>
                                    {{ $cliente->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('cliente_id')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Nombre --}}
                    <div class="mb-4">
                        <label for="nombre" class="block text-zinc-700 dark:text-zinc-200 font-semibold mb-1">Nombre
                            del centro</label>
                        <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}"
                            class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring focus:border-blue-400"
                            required>
                        @error('nombre')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Dirección --}}
                    <div class="mb-4">
                        <label for="direccion"
                            class="block text-zinc-700 dark:text-zinc-200 font-semibold mb-1">Dirección</label>
                        <input type="text" name="direccion" id="direccion" value="{{ old('direccion') }}"
                            class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring focus:border-blue-400">
                        @error('direccion')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Ciudad y Región --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="ciudad"
                                class="block text-zinc-700 dark:text-zinc-200 font-semibold mb-1">Ciudad</label>
                            <input type="text" name="ciudad" id="ciudad" value="{{ old('ciudad') }}"
                                class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring focus:border-blue-400">
                            @error('ciudad')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label for="region"
                                class="block text-zinc-700 dark:text-zinc-200 font-semibold mb-1">Región</label>
                            <input type="text" name="region" id="region" value="{{ old('region') }}"
                                class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring focus:border-blue-400">
                            @error('region')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Teléfono --}}
                    <div class="mb-6">
                        <label for="telefono"
                            class="block text-zinc-700 dark:text-zinc-200 font-semibold mb-1">Teléfono</label>
                        <input type="text" name="telefono" id="telefono" value="{{ old('telefono') }}"
                            class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring focus:border-blue-400">
                        @error('telefono')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Botones --}}
                    <div class="flex justify-end gap-2">
                        <button type="submit"
                            class="bg-zinc-200 dark:bg-zinc-700 text-zinc-100 dark:text-zinc-100 font-semibold px-4 py-2 rounded-lg hover:bg-zinc-300 dark:hover:bg-zinc-600 transition flex items-center gap-2">
                            <i class="fa fa-check"></i>
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
