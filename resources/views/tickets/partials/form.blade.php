<form action="{{ route('tickets.update', $ticket) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="mb-6 p-4 bg-zinc-50 dark:bg-zinc-800 rounded-lg">
        <!-- Encabezado del Ticket -->
        <div class="mb-4 pb-4 border-b border-zinc-200 dark:border-zinc-700">
            <h3 class="text-lg font-semibold text-zinc-800 dark:text-white mb-2">
                Ticket: {{ $ticket->numero_ticket }}
            </h3>
            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                Cliente: <span class="font-medium">{{ $ticket->cliente }}</span> |
                Solicitante: <span class="font-medium">{{ $ticket->nombre_apellido }}</span>
            </p>
        </div>

        <!-- Información de la Falla -->
        <div class="space-y-4">
            <h3 class="text-lg font-semibold text-zinc-800 dark:text-white mb-2"> Información de la Falla</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Equipo -->
                <div>
                    <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Equipo:</p>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">
                        <span class="font-medium">ID:</span> {{ $ticket->id_numero_equipo ?: 'N/A' }}
                    </p>
                </div>
                <div>
                    <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Marca/Modelo:</p>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">
                        {{ $ticket->modelo_maquina ?: 'N/A' }}
                    </p>
                </div>
                <div>
                    <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Momento de la Falla:</p>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $ticket->momento_falla }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Falla Presentada:</p>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $ticket->falla_presentada }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Estado -->
        <div>
            <label for="estado" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                Estado <span class="text-red-500">*</span>
            </label>
            <select name="estado" id="estado" required
                class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-blue-500 @error('estado') border-red-500 @enderror">
                <option value="">Seleccionar estado</option>
                <option value="pendiente" {{ old('estado', $ticket->estado) == 'pendiente' ? 'selected' : '' }}>
                    Pendiente
                </option>
                <option value="reagendar" {{ old('estado', $ticket->estado) == 'reagendar' ? 'selected' : '' }}>
                    Reagendar Visita
                </option>
                <option value="completado" {{ old('estado', $ticket->estado) == 'completado' ? 'selected' : '' }}>
                    Completado
                </option>
                <!-- Removido "en_proceso" para evitar cambios automáticos -->
            </select>
            @error('estado')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Técnico Asignado -->
        <div>
            <label for="tecnico_asignado_id" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                Técnico Asignado
            </label>
            <select name="tecnico_asignado_id" id="tecnico_asignado_id"
                class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-blue-500 @error('tecnico_asignado_id') border-red-500 @enderror">
                <option value="">Sin asignar</option>
                @foreach ($tecnicos as $tecnico)
                    <option value="{{ $tecnico->id }}"
                        {{ old('tecnico_asignado_id', $ticket->tecnico_asignado_id) == $tecnico->id ? 'selected' : '' }}>
                        {{ $tecnico->name }}
                    </option>
                @endforeach
            </select>
            @error('tecnico_asignado_id')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Fecha de Visita - Mejorada para reagendamiento -->
        <div class="md:col-span-2">
            <label for="fecha_visita" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                Fecha y Hora de Visita
                @if ($ticket->fecha_visita)
                    <span class="text-xs text-zinc-500 ml-2">
                        (Actual: {{ $ticket->fecha_visita->format('d/m/Y H:i') }})
                    </span>
                @endif
            </label>
            <input type="datetime-local" name="fecha_visita" id="fecha_visita"
                value="{{ old('fecha_visita', $ticket->fecha_visita ? $ticket->fecha_visita->format('Y-m-d\TH:i') : '') }}"
                class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-blue-500 @error('fecha_visita') border-red-500 @enderror">
            @error('fecha_visita')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror

            <!-- Mensaje informativo para reagendamiento -->
            <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                💡 Si cambias la fecha y seleccionas "Reagendar Visita", se registrará automáticamente en el historial
                del ticket.
            </p>
        </div>

        <!-- Motivo del Reagendamiento (aparece solo cuando se selecciona reagendar) -->
        <div class="md:col-span-2" id="motivo_reagendamiento" style="display: none;">
            <label for="motivo_reagendamiento_texto"
                class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                Motivo del Reagendamiento
            </label>
            <textarea name="motivo_reagendamiento" id="motivo_reagendamiento_texto" rows="2"
                class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-blue-500"
                placeholder="Explique el motivo del reagendamiento (ej: Cliente no disponible, técnico enfermo, etc.)">{{ old('motivo_reagendamiento') }}</textarea>
        </div>

        <!-- Acciones Realizadas -->
        <div class="md:col-span-2">
            <label for="acciones_realizadas" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                Acciones Realizadas
            </label>
            <textarea name="acciones_realizadas" id="acciones_realizadas" rows="4"
                class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-blue-500 @error('acciones_realizadas') border-red-500 @enderror"
                placeholder="Describa las acciones realizadas para resolver el ticket...">{{ old('acciones_realizadas', $ticket->acciones_realizadas) }}</textarea>
            @error('acciones_realizadas')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="flex justify-end gap-3 mt-6">
        <a href="{{ route('tickets.index') }}"
            class="bg-zinc-200 dark:bg-zinc-700 hover:bg-zinc-300 dark:hover:bg-zinc-600 text-zinc-800 dark:text-white font-semibold px-6 py-2 rounded-lg transition">
            Cancelar
        </a>
        <button type="submit"
            class="bg-zinc-600 hover:bg-zinc-700 text-white font-semibold px-6 py-2 rounded-lg transition">
            <i class="fa fa-save"></i>
            Actualizar Ticket
        </button>
    </div>
</form>

<script>
    // Mostrar/ocultar campo de motivo según el estado seleccionado
    document.getElementById('estado').addEventListener('change', function() {
        const motivoDiv = document.getElementById('motivo_reagendamiento');
        if (this.value === 'reagendar') {
            motivoDiv.style.display = 'block';
        } else {
            motivoDiv.style.display = 'none';
        }
    });

    // Ejecutar al cargar la página si ya está seleccionado "reagendar"
    document.addEventListener('DOMContentLoaded', function() {
        const estadoSelect = document.getElementById('estado');
        if (estadoSelect.value === 'reagendar') {
            document.getElementById('motivo_reagendamiento').style.display = 'block';
        }
    });
</script>
