<x-layouts.app :title="'Nuevo Cliente'">
    <div class="max-w-3xl mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">Nuevo Cliente</h1>
        @if ($errors->any())
            <div class="mb-4 p-3 rounded bg-red-50 text-red-700 text-sm">
                <b>Corrige los siguientes campos:</b>
                <ul class="list-disc ml-5">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <form method="POST" action="{{ route('clientes.store') }}" class="grid gap-4">
                <div
                    class="bg-white dark:bg-zinc-800 rounded-lg shadow-md p-6 border border-zinc-200 dark:border-zinc-700">
                    <h3 class="text-lg font-bold text-zinc-800 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fa fa-info-circle text-blue-500"></i> Información del Cliente
                    </h3>
                    @csrf
                    <div class="space-y-4">

                        <div class="mb-4">
                            <label class="block text-zinc-700 dark:text-zinc-200 font-semibold mb-1">Nombre</label>
                            <input name="nombre" value="{{ old('nombre') }}" type="text"
                                class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring focus:border-blue-400"
                                required>
                            @error('nombre')
                                <p class="text-red-600 text-sm">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-zinc-700 dark:text-zinc-200 font-semibold mb-11">Razón
                                Social</label>
                            <input name="razon_social" value="{{ old('razon_social') }}"
                                class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring focus:border-blue-400">
                            @error('razon_social')
                                <p class="text-red-600 text-sm">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-zinc-700 dark:text-zinc-200 font-semibold mb-1">RUT</label>
                            <input name="rut" value="{{ old('rut') }}"
                                class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring focus:border-blue-400">
                            @error('rut')
                                <p class="text-red-600 text-sm">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <h3
                                class="text-lg font-bold text-zinc-800 dark:text-white mb-4 flex items-center gap-2 mt-2">
                                <i class="fa fa-phone text-green-500"></i> Datos de Contacto
                            </h3>
                            <div class="mb-4">
                                <label class="block text-zinc-700 dark:text-zinc-200 font-semibold mb-1">Email</label>
                                <input name="email" type="email" value="{{ old('email') }}"
                                    class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring focus:border-blue-400">
                                @error('email')
                                    <p class="text-red-600 text-sm">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label
                                    class="block text-zinc-700 dark:text-zinc-200 font-semibold mb-1">Teléfono</label>
                                <input name="telefono" value="{{ old('telefono') }}"
                                    class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring focus:border-blue-400">
                                @error('telefono')
                                    <p class="text-red-600 text-sm">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end gap-3">
                            <button
                                class="bg-zinc-200 dark:bg-zinc-700 hover:bg-zinc-300 dark:hover:bg-zinc-600 text-zinc-800 dark:text-white font-semibold px-6 py-2 rounded-lg transition">
                                <i class="fa fa-save"></i> Guardar</button>
                            <a href="{{ route('clientes.index') }}"
                                class="bg-zinc-600 hover:bg-zinc-700 text-white font-semibold px-6 py-2 rounded-lg transition">
                                <i class="fa fa-arrow-left"></i> Cancelar</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
</x-layouts.app>
