<x-layouts.app :title="$title ?? 'Clientes'">
    <div class="min-h-screen px-4 py-8 bg-zinc-50 dark:bg-zinc-800 transition-all">
        <div class="max-w-6xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-zinc-800 dark:text-white">Clientes</h1>
                <a href="{{ route('clientes.create') }}">
                    <button type="button"
                        class="bg-zinc-200 dark:bg-zinc-700 text-zinc-100 dark:text-zinc-100 font-semibold px-4 py-2 rounded-lg hover:bg-zinc-300 dark:hover:bg-zinc-600 transition flex items-center gap-2">
                        <i class="fa fa-plus"></i>
                        Nuevo Cliente
                    </button>
                </a>
            </div>
            <div class="overflow-x-auto rounded-lg shadow bg-blue-100 dark:bg-zinc-900">
                @php
                    $tableRowClass = 'border-transparent hover:border-zinc-200 dark:hover:border-transparent 
                                       bg-zinc-50 dark:bg-zinc-900 
                                       hover:bg-zinc-800/5 dark:hover:bg-white/[7%] 
                                       text-zinc-500 dark:text-white/80 
                                       hover:text-zinc-800 dark:hover:text-white 
                                       transition-all duration-200';
                @endphp
                <table class="w-full table-auto text-left text-sm">
                    <thead>
                        <tr class="bg-white dark:bg-zinc-700">
                            <th class="p-3 text-zinc-900 dark:text-white font-semibold">Nombre</th>
                            <th class="p-3 text-zinc-900 dark:text-white font-semibold">Razon Social</th>
                            <th class="p-3 text-zinc-900 dark:text-white font-semibold">RUT</th>
                            <th class="p-3 text-zinc-900 dark:text-white font-semibold">Teléfono</th>
                            <th class="p-3 text-center text-zinc-900 dark:text-white font-semibold">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($clientes as $cliente)
                            <tr class="{{ $tableRowClass }}">
                                <td class="p-3 text-zinc-900 dark:text-zinc-100 font-medium">
                                    {{ $cliente->nombre }}</td>
                                <td class="p-3 text-zinc-700 dark:text-zinc-300">{{ $cliente->razon_social }}</td>
                                <td class="p-3 text-zinc-700 dark:text-zinc-300">{{ $cliente->rut }}</td>
                                <td class="p-3 text-zinc-700 dark:text-zinc-300">{{ $cliente->telefono }}</td>

                                <td class="p-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('clientes.show', $cliente) }}"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-500 hover:bg-blue-600 text-white transition-colors duration-200"
                                            title="Ver">
                                            <i class="fa fa-eye text-sm"></i>
                                        </a>
                                        <a href="{{ route('clientes.edit', $cliente) }}"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-green-500 hover:bg-green-600 text-white transition-colors duration-200"
                                            title="Editar">
                                            <i class="fa fa-pencil text-sm"></i>
                                        </a>
                                        <form action="{{ route('clientes.destroy', $cliente) }}" method="POST"
                                            style="display:inline;" onsubmit="return confirm('¿Eliminar cliente?');">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-500 hover:bg-red-600 text-white transition-colors duration-200"
                                                title="Eliminar">
                                                <i class="fa fa-trash text-sm"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="p-4 bg-white dark:bg-zinc-800">
                    {{ $clientes->links() }}
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
