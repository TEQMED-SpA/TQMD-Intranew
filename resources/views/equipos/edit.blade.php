<x-layouts.app :title="'Editar Equipo'">
    <div class="max-w-4xl mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-4">Editar Equipo</h1>
        @if (session('error'))
            <div class="mb-4 text-red-600">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('equipos.update', $equipo) }}" class="grid gap-4">
            @csrf @method('PUT')

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block mb-1">Cliente</label>
                    <select id="cliente_id" name="cliente_id" class="w-full border rounded px-3 py-2" required>
                        @foreach ($clientes as $c)
                            <option value="{{ $c->id }}" @selected(old('cliente_id', $equipo->cliente_id) == $c->id)>{{ $c->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block mb-1">Centro Médico</label>
                    <select id="centro_medico_id" name="centro_medico_id" class="w-full border rounded px-3 py-2"
                        required>
                        @foreach ($centros as $cm)
                            <option value="{{ $cm->id }}" @selected(old('centro_medico_id', $equipo->centro_medico_id) == $cm->id)>{{ $cm->centro_dialisis }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block mb-1">Nombre</label><input name="nombre"
                        value="{{ old('nombre', $equipo->nombre) }}" class="w-full border rounded px-3 py-2" required>
                </div>
                <div>
                    <label class="block mb-1">Estado</label>
                    <select name="estado" class="w-full border rounded px-3 py-2">
                        <option value="">—</option>
                        <option value="operativo" @selected(old('estado', $equipo->estado) === 'operativo')>Operativo
                        </option>
                        <option value="en observación" @selected(old('estado', $equipo->estado) === 'en observación')>En Observación
                        </option>
                        <option value="fuera de servicio" @selected(old('estado', $equipo->estado) === 'fuera de servicio')>Fuera de Servicio</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div><label class="block mb-1">Marca</label><input name="marca"
                        value="{{ old('marca', $equipo->marca) }}" class="w-full border rounded px-3 py-2"></div>
                <div><label class="block mb-1">Modelo</label><input name="modelo"
                        value="{{ old('modelo', $equipo->modelo) }}" class="w-full border rounded px-3 py-2"></div>
                <div><label class="block mb-1">SKU (interno)</label><input name="sku"
                        value="{{ old('sku', $equipo->sku) }}" class="w-full border rounded px-3 py-2"></div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div><label class="block mb-1">ID máquina (centro)</label><input name="id_maquina"
                        value="{{ old('id_maquina', $equipo->id_maquina) }}" class="w-full border rounded px-3 py-2">
                </div>
                <div><label class="block mb-1">Nº serie</label><input name="numero_serie"
                        value="{{ old('numero_serie', $equipo->numero_serie) }}"
                        class="w-full border rounded px-3 py-2">
                </div>
                <div><label class="block mb-1">Horas de uso</label><input type="number" min="0" name="horas_uso"
                        value="{{ old('horas_uso', $equipo->horas_uso) }}" class="w-full border rounded px-3 py-2">
                </div>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div><label class="block mb-1">Últ. mantención</label><input type="date" name="ultima_mantencion"
                        value="{{ old('ultima_mantencion', $equipo->ultima_mantencion?->format('Y-m-d')) }}"
                        class="w-full border rounded px-3 py-2"></div>
                <div><label class="block mb-1">Próx. mantención</label><input type="date" name="proxima_mantencion"
                        value="{{ old('proxima_mantencion', $equipo->proxima_mantencion?->format('Y-m-d')) }}"
                        class="w-full border rounded px-3 py-2"></div>
                <div></div>
                <label class="block mb-1">Comentarios</label>
                <textarea name="comentarios" rows="3" class="w-full border rounded px-3 py-2">{{ old('comentarios', $equipo->comentarios) }}</textarea>
            </div>
            <button type="submit"
                class="bg-zinc-800 hover:bg-zinc-900 text-white px-4 py-2 rounded">Actualizar</button>
        </form>
    </div>
</x-layouts.app>
