<x-layouts.app :title="$title ?? 'Detalle Repuesto'">
    <div class="min-h-screen px-4 py-8 bg-zinc-50 dark:bg-zinc-800 transition-all">
        <div class="max-w-2xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-zinc-800 dark:text-white">Detalle Repuesto</h1>
                <a href="{{ route('repuestos.index') }}">
                    <button type="button"
                        class="bg-zinc-200 dark:bg-zinc-700 text-zinc-100 dark:text-zinc-100 font-semibold px-4 py-2 rounded-lg hover:bg-zinc-300 dark:hover:bg-zinc-600 transition flex items-center gap-2">
                        <i class="fa fa-arrow-left"></i>
                        Volver
                    </button>
                </a>
            </div>
            <div class="rounded-lg shadow bg-white dark:bg-zinc-900 p-6">
                <ul class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    <li class="py-3 flex justify-between items-center">
                        <span class="font-semibold text-zinc-700 dark:text-zinc-200">Serie:</span>
                        <span class="text-zinc-900 dark:text-zinc-100">{{ $repuesto->producto_serie }}</span>
                    </li>
                    <li class="py-3 flex justify-between items-center">
                        <span class="font-semibold text-zinc-700 dark:text-zinc-200">Nombre:</span>
                        <span class="text-zinc-900 dark:text-zinc-100">{{ $repuesto->producto_nombre }}</span>
                    </li>
                    <li class="py-3 flex justify-between items-center">
                        <span class="font-semibold text-zinc-700 dark:text-zinc-200">Modelo:</span>
                        <span class="text-zinc-900 dark:text-zinc-100">{{ $repuesto->producto_modelo }}</span>
                    </li>
                    <li class="py-3 flex justify-between items-center">
                        <span class="font-semibold text-zinc-700 dark:text-zinc-200">Marca:</span>
                        <span class="text-zinc-900 dark:text-zinc-100">{{ $repuesto->producto_marca }}</span>
                    </li>
                    <li class="py-3 flex justify-between items-center">
                        <span class="font-semibold text-zinc-700 dark:text-zinc-200">Ubicación:</span>
                        <span class="text-zinc-900 dark:text-zinc-100">{{ $repuesto->producto_ubicacion }}</span>
                    </li>
                    <li class="py-3 flex justify-between items-center">
                        <span class="font-semibold text-zinc-700 dark:text-zinc-200">Descripción:</span>
                        <span class="text-zinc-900 dark:text-zinc-100">{{ $repuesto->producto_descripcion }}</span>
                    </li>
                    <li class="py-3 flex justify-between items-center">
                        <span class="font-semibold text-zinc-700 dark:text-zinc-200">Stock:</span>
                        <span class="text-zinc-900 dark:text-zinc-100">{{ $repuesto->producto_stock }}</span>
                    </li>
                    <li class="py-3 flex justify-between items-center">
                        <span class="font-semibold text-zinc-700 dark:text-zinc-200">Foto:</span>
                        <span>
                            @if ($repuesto->producto_foto)
                                <img src="{{ asset('storage/' . $repuesto->producto_foto) }}" alt="Foto"
                                    class="h-20 w-20 object-cover rounded" />
                            @else
                                <span class="text-zinc-500">Sin foto</span>
                            @endif
                        </span>
                    </li>
                    <li class="py-3 flex justify-between items-center">
                        <span class="font-semibold text-zinc-700 dark:text-zinc-200">Categoría:</span>
                        <span class="text-zinc-900 dark:text-zinc-100">{{ $repuesto->categoria?->nombre }}</span>
                    </li>
                    <li class="py-3 flex justify-between items-center">
                        <span class="font-semibold text-zinc-700 dark:text-zinc-200">Estado:</span>
                        @php
                            $isActive = ($repuesto->producto_estado ?? '') === 'activo';
                        @endphp
                        <span
                            class="inline-block rounded-full px-3 py-1 text-xs font-semibold transition-colors duration-200
                            {{ $isActive
                                ? 'bg-green-200 dark:bg-green-800 text-green-900 dark:text-zinc-800'
                                : 'bg-red-200 dark:bg-red-800 text-red-900 dark:text-zinc-800' }}">
                            {{ $isActive ? 'Activo' : 'Inactivo' }}
                        </span>
                    </li>
                    <li class="py-3 flex justify-between items-center">
                        <span class="font-semibold text-zinc-700 dark:text-zinc-200">Creado:</span>
                        <span
                            class="text-zinc-700 dark:text-zinc-300">{{ $repuesto->created_at?->format('d-m-Y H:i') }}</span>
                    </li>
                </ul>
                <div class="flex justify-end mt-6 gap-2">
                    <a href="{{ route('repuestos.edit', $repuesto) }}">
                        <button type="button"
                            class="bg-green-500 hover:bg-green-600 text-white font-semibold px-4 py-2 rounded-lg transition flex items-center gap-2">
                            <i class="fa fa-pencil"></i>
                            Editar
                        </button>
                    </a>
                    <form action="{{ route('repuestos.destroy', $repuesto) }}" method="POST"
                        onsubmit="return confirm('¿Eliminar repuesto?');">
                        @csrf @method('DELETE')
                        <button type="submit"
                            class="bg-red-500 hover:bg-red-600 text-white font-semibold px-4 py-2 rounded-lg transition flex items-center gap-2">
                            <i class="fa fa-trash"></i>
                            Eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
