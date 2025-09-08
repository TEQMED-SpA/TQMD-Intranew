<x-layouts.app :title="$title ?? 'Nuevo Centro Médico'">
    <div class="min-h-screen px-4 py-8 bg-zinc-50 dark:bg-zinc-800 transition-all">
        <div class="max-w-2xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-zinc-800 dark:text-white">Nuevo Centro Médico</h1>
                <a href="{{ route('centros_medicos.index') }}">
                    <button type="button"
                        class="bg-zinc-200 dark:bg-zinc-700 text-zinc-800 dark:text-zinc-100 font-semibold px-4 py-2 rounded-lg hover:bg-zinc-300 dark:hover:bg-zinc-600 transition flex items-center gap-2">
                        <i class="fa fa-arrow-left"></i>
                        Volver
                    </button>
                </a>
            </div>
            <div class="rounded-lg shadow bg-white dark:bg-zinc-900 p-6">
                <form action="{{ route('centros_medicos.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="name"
                            class="block text-zinc-700 dark:text-zinc-200 font-semibold mb-1">Nombre</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}"
                            class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring focus:border-blue-400"
                            required>
                        @error('name')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="direccion"
                            class="block text-zinc-700 dark:text-zinc-200 font-semibold mb-1">Dirección</label>
                        <input type="text" name="direccion" id="direccion" value="{{ old('direccion') }}"
                            class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring focus:border-blue-400">
                        @error('direccion')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="telefono"
                            class="block text-zinc-700 dark:text-zinc-200 font-semibold mb-1">Teléfono</label>
                        <input type="text" name="telefono" id="telefono" value="{{ old('telefono') }}"
                            class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring focus:border-blue-400">
                        @error('telefono')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="status"
                            class="block text-zinc-700 dark:text-zinc-200 font-semibold mb-1">Estado</label>
                        <select name="status" id="status"
                            class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring focus:border-blue-400">
                            <option value="activo" @selected(old('status') == 'activo')>Activo</option>
                            <option value="inactivo" @selected(old('status') == 'inactivo')>Inactivo</option>
                        </select>
                        @error('status')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="submit"
                            class="bg-[#00618E] text-white font-semibold px-4 py-2 rounded-lg hover:bg-[#004a6b] transition flex items-center gap-2">
                            <i class="fa fa-check"></i>
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
