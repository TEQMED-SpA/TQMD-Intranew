@csrf

<div class="mb-4">
    <label for="name" class="block text-zinc-700 dark:text-zinc-200 font-semibold mb-1">Nombre</label>
    <input type="text" name="name" id="name" value="{{ old('name', $user->name ?? '') }}"
        class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring focus:border-blue-400"
        required>
    @error('name')
        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
    @enderror
</div>
<div class="mb-4">
    <label for="email" class="block text-zinc-700 dark:text-zinc-200 font-semibold mb-1">Email</label>
    <input type="email" name="email" id="email" value="{{ old('email', $user->email ?? '') }}"
        class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring focus:border-blue-400"
        required>
    @error('email')
        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
    @enderror
</div>
<div class="mb-4">
    <label for="rol_id" class="block text-zinc-700 dark:text-zinc-200 font-semibold mb-1">Rol</label>
    <select name="rol_id" id="rol_id"
        class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring focus:border-blue-400"
        required>
        <option value="">-- Seleccione --</option>
        @foreach ($roles as $rol)
            <option value="{{ $rol->id }}" @selected(old('rol_id', $user->rol_id ?? '') == $rol->id)>
                {{ $rol->nombre }}
            </option>
        @endforeach
    </select>
    @error('rol_id')
        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
    @enderror
</div>
<div class="mb-4">
    <label for="estado" class="block text-zinc-700 dark:text-zinc-200 font-semibold mb-1">Estado</label>
    <select name="activo" id="estado"
        class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring focus:border-blue-400">
        <option value="1" @selected(old('activo', $user->activo ?? '') == '1')>Activo</option>
        <option value="0" @selected(old('activo', $user->activo ?? '') == '0')>Inactivo</option>
    </select>
    @error('activo')
        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
    @enderror
</div>
<div class="mb-6">
    <label for="password" class="block text-zinc-700 dark:text-zinc-200 font-semibold mb-1">
        Contraseña <span class="text-xs text-zinc-400">(dejar en blanco para no cambiarla)</span>
    </label>
    <input type="password" name="password" id="password"
        class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring focus:border-blue-400">
    @error('password')
        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
    @enderror
</div>
<div class="mb-6">
    <label for="password_confirmation" class="block text-zinc-700 dark:text-zinc-200 font-semibold mb-1">
        Confirmar contraseña
    </label>
    <input type="password" name="password_confirmation" id="password_confirmation"
        class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring focus:border-blue-400">
    @error('password_confirmation')
        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
    @enderror
</div>
