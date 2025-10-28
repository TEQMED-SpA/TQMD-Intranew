<x-layouts.app :title="'Cliente: ' . $cliente->nombre">
    <div class="max-w-4xl mx-auto px-4 py-8">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-bold">{{ $cliente->nombre }}</h1>
            <div class="flex gap-2">
                @role('admin|auditor')
                    <a href="{{ route('clientes.edit', $cliente) }}"
                        class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded">Editar</a>
                @endrole
                <a href="{{ route('clientes.index') }}" class="border px-3 py-2 rounded">Volver</a>
            </div>
        </div>

        <div class="space-y-2">
            <div><b>Email:</b> {{ $cliente->email ?? '—' }}</div>
            <div><b>Teléfono:</b> {{ $cliente->telefono ?? '—' }}</div>
            <div><b>Estado:</b> {{ ucfirst($cliente->status ?? '—') }}</div>
        </div>

        <h2 class="mt-6 mb-2 font-semibold">Centros</h2>
        <ul class="list-disc ps-6">
            @forelse($cliente->centros as $c)
                <li><a href="{{ route('centros_medicos.show', $c) }}"
                        class="text-blue-600 hover:underline">{{ $c->centro_dialisis }}</a></li>
            @empty
                <li class="text-zinc-500">Sin centros</li>
            @endforelse
        </ul>
    </div>
</x-layouts.app>
