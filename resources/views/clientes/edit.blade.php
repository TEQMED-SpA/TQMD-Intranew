<x-layouts.app :title="'Editar Cliente'">
    <div class="max-w-3xl mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">Editar Cliente</h1>
        @if (session('error'))
            <div class="mb-4 text-red-600">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('clientes.update', $cliente) }}" class="grid gap-4">
            @csrf @method('PUT')

            <div>
                <label class="block mb-1">Nombre</label>
                <input name="nombre" value="{{ old('nombre', $cliente->nombre) }}" class="w-full border rounded px-3 py-2"
                    required>
                @error('nombre')
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block mb-1">Email</label>
                    <input name="email" type="email" value="{{ old('email', $cliente->email) }}"
                        class="w-full border rounded px-3 py-2">
                    @error('email')
                        <p class="text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block mb-1">Teléfono</label>
                    <input name="telefono" value="{{ old('telefono', $cliente->telefono) }}"
                        class="w-full border rounded px-3 py-2">
                    @error('telefono')
                        <p class="text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block mb-1">Estado</label>
                <select name="status" class="w-full border rounded px-3 py-2">
                    <option value="activo" @selected(old('status', $cliente->status) === 'activo')>Activo</option>
                    <option value="inactivo"@selected(old('status', $cliente->status) === 'inactivo')>Inactivo</option>
                </select>
            </div>

            <div class="flex gap-3">
                <button class="bg-emerald-600 text-white px-4 py-2 rounded">Guardar</button>
                <a href="{{ route('clientes.show', $cliente) }}" class="border px-4 py-2 rounded">Cancelar</a>
            </div>
        </form>
    </div>
</x-layouts.app>
