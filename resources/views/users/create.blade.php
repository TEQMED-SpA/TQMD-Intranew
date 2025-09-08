<x-layouts.app :title="$title ?? 'Nuevo Usuario'">
    <div class="min-h-screen px-4 py-8 bg-zinc-50 dark:bg-zinc-800 transition-all">
        <div class="max-w-2xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-zinc-800 dark:text-white">Nuevo Usuario</h1>
                <a href="{{ route('users.index') }}">
                    <button type="button"
                        class="bg-zinc-200 dark:bg-zinc-700 text-zinc-100 dark:text-zinc-100 font-semibold px-4 py-2 rounded-lg hover:bg-zinc-300 dark:hover:bg-zinc-600 transition flex items-center gap-2">
                        <i class="fa fa-arrow-left"></i>
                        Volver
                    </button>
                </a>
            </div>
            <div class="rounded-lg shadow bg-white dark:bg-zinc-900 p-6">
                <form action="{{ route('users.store') }}" method="POST">
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
                        <label for="email"
                            class="block text-zinc-700 dark:text-zinc-200 font-semibold mb-1">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}"
                            class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring focus:border-blue-400"
                            required>
                        @error('email')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="rol_id"
                            class="block text-zinc-700 dark:text-zinc-200 font-semibold mb-1">Rol</label>
                        <select name="rol_id" id="rol_id"
                            class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring focus:border-blue-400"
                            required>
                            <option value="">-- Seleccione --</option>
                            @foreach ($roles as $rol)
                                <option value="{{ $rol->id }}" @selected(old('rol_id') == $rol->id)>{{ $rol->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('rol_id')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="estado"
                            class="block text-zinc-700 dark:text-zinc-200 font-semibold mb-1">Estado</label>
                        <select name="estado" id="estado"
                            class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring focus:border-blue-400">
                            <option value="1" @selected(old('estado') == '1')>Activo</option>
                            <option value="0" @selected(old('estado') == '0')>Inactivo</option>
                        </select>
                        @error('estado')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="password"
                            class="block text-zinc-700 dark:text-zinc-200 font-semibold mb-1">Contraseña</label>
                        <input type="password" name="password" id="password"
                            class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring focus:border-blue-400"
                            required>
                        @error('password')
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
