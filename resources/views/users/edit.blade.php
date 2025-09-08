<x-layouts.app :title="$title ?? 'Editar Usuario'">
    <div class="min-h-screen px-4 py-8 bg-zinc-50 dark:bg-zinc-800 transition-all">
        <div class="max-w-2xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-zinc-800 dark:text-white">Editar Usuario</h1>
                <a href="{{ route('users.index') }}">
                    <button type="button"
                        class="bg-zinc-200 dark:bg-zinc-700 text-zinc-800 dark:text-zinc-100 font-semibold px-4 py-2 rounded-lg hover:bg-zinc-300 dark:hover:bg-zinc-600 transition flex items-center gap-2">
                        <i class="fa fa-arrow-left"></i>
                        Volver
                    </button>
                </a>
            </div>
            <div class="rounded-lg shadow bg-white dark:bg-zinc-900 p-6">
                <form action="{{ route('users.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label for="name"
                            class="block text-zinc-700 dark:text-zinc-200 font-semibold mb-1">Nombre</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                            class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring focus:border-blue-400"
                            required>
                        @error('name')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="email"
                            class="block text-zinc-700 dark:text-zinc-200 font-semibold mb-1">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
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
                                <option value="{{ $rol->id }}" @selected(old('rol_id', $user->rol_id) == $rol->id)>{{ $rol->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('rol_id')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="status"
                            class="block text-zinc-700 dark:text-zinc-200 font-semibold mb-1">Estado</label>
                        <select name="status" id="status"
                            class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring focus:border-blue-400">
                            <option value="activo" @selected(old('status', $user->status) == 'activo')>Activo</option>
                            <option value="inactivo" @selected(old('status', $user->status) == 'inactivo')>Inactivo</option>
                        </select>
                        @error('status')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-6">
                        <label for="password"
                            class="block text-zinc-700 dark:text-zinc-200 font-semibold mb-1">Contraseña <span
                                class="text-xs text-zinc-400">(solo si deseas cambiarla)</span></label>
                        <input type="password" name="password" id="password"
                            class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring focus:border-blue-400">
                        @error('password')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="submit"
                            class="bg-[#00618E] text-zinc-700 dark:text-zinc-200 font-semibold px-4 py-2 rounded-lg hover:bg-[#004a6b] transition flex items-center gap-2">
                            <i class="fa fa-check"></i>
                            Actualizar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
