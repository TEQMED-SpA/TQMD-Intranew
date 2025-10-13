<form method="POST" action="{{ route('repuestos.store') }}" enctype="multipart/form-data" class="max-w-7xl mx-auto">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Columna 1: Información Básica -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-md p-6 border border-zinc-200 dark:border-zinc-700">
            <h3 class="text-lg font-bold text-zinc-800 dark:text-white mb-4 flex items-center gap-2">
                <i class="fa fa-info-circle text-blue-500"></i>
                Información Básica
            </h3>
            <div class="space-y-4">
                {{-- Serie --}}
                <div>
                    <label for="serie"
                        class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200 mb-1">Serie</label>
                    <input type="text" name="serie" id="serie" placeholder="Ej: ABC123-456"
                        value="{{ old('serie', $repuesto->serie ?? '') }}"
                        class="w-full px-3 py-2 text-sm border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                    @error('serie')
                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Nombre --}}
                <div>
                    <label for="nombre"
                        class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200 mb-1">Nombre del
                        Repuesto</label>
                    <input type="text" name="nombre" id="nombre" placeholder="Ej: Bomba de concentrado"
                        value="{{ old('nombre', $repuesto->nombre ?? '') }}"
                        class="w-full px-3 py-2 text-sm border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('nombre')
                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Modelo --}}
                <div>
                    <label for="modelo"
                        class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200 mb-1">Modelo</label>
                    <select name="modelo" id="modelo"
                        class="select2-modelo w-full px-3 py-2 text-sm border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Seleccionar...</option>
                        @foreach ($modelos as $modelo)
                            <option value="{{ $modelo }}" @selected(old('modelo', $repuesto->modelo ?? '') == $modelo)>{{ $modelo }}
                            </option>
                        @endforeach
                        <option value="__nuevo__">➕ Agregar nuevo...</option>
                    </select>
                    @error('modelo')
                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Marca --}}
                <div>
                    <label for="marca"
                        class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200 mb-1">Marca</label>
                    <select name="marca" id="marca"
                        class="select2-marca w-full px-3 py-2 text-sm border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Seleccionar...</option>
                        @foreach ($marcas as $marca)
                            <option value="{{ $marca }}" @selected(old('marca', $repuesto->marca ?? '') == $marca)>{{ $marca }}
                            </option>
                        @endforeach
                        <option value="__nuevo__">➕ Agregar nueva...</option>
                    </select>
                    @error('marca')
                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Columna 2: Stock y Ubicación -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-md p-6 border border-zinc-200 dark:border-zinc-700">
            <h3 class="text-lg font-bold text-zinc-800 dark:text-white mb-4 flex items-center gap-2">
                <i class="fa fa-cube text-green-500"></i>
                Stock y Ubicación
            </h3>
            <div class="space-y-4">
                {{-- Stock --}}
                <div>
                    <label for="stock"
                        class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200 mb-1">Cantidad en
                        Stock</label>
                    <input type="number" name="stock" id="stock" min="0" step="1"
                        placeholder="Ej: 25" value="{{ old('stock', $repuesto->stock ?? '') }}"
                        class="w-full px-3 py-2 text-sm border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('stock')
                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Ubicación --}}
                <div>
                    <label for="ubicacion"
                        class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200 mb-1">Ubicación en
                        Almacén</label>
                    <select name="ubicacion" id="ubicacion"
                        class="select2-ubicacion w-full px-3 py-2 text-sm border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Seleccionar...</option>
                        @foreach ($ubicaciones as $ubicacion)
                            <option value="{{ $ubicacion }}" @selected(old('ubicacion', $repuesto->ubicacion ?? '') == $ubicacion)>{{ $ubicacion }}
                            </option>
                        @endforeach
                        <option value="__nuevo__">➕ Agregar nueva...</option>
                    </select>
                    @error('ubicacion')
                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Categoría --}}
                <div>
                    <label for="categoria_id"
                        class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200 mb-1">Categoría</label>
                    <select name="categoria_id" id="categoria_id" required
                        class="select2-categoria w-full px-3 py-2 text-sm border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="" disabled
                            {{ !old('categoria_id', $repuesto->categoria_id ?? '') ? 'selected' : '' }}>Seleccionar...
                        </option>
                        @foreach ($categorias as $categoria)
                            <option value="{{ $categoria->categoria_id }}" @selected(old('categoria_id', $repuesto->categoria_id ?? '') == $categoria->categoria_id)>
                                {{ $categoria->nombre }}
                            </option>
                        @endforeach
                        <option value="__nuevo__">➕ Agregar nueva...</option>
                    </select>
                    @error('categoria_id')
                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Estado --}}
                <div>
                    <label for="estado"
                        class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200 mb-1">Estado</label>
                    <select name="estado" id="estado"
                        class="w-full px-3 py-2 text-sm border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="activo" @selected(old('estado', $repuesto->estado ?? 'activo') === 'activo')>✅ Activo</option>
                        <option value="inactivo" @selected(old('estado', $repuesto->estado ?? '') === 'inactivo')>❌ Inactivo</option>
                    </select>
                    @error('estado')
                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Columna 3: Descripción y Foto -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-md p-6 border border-zinc-200 dark:border-zinc-700">
            <h3 class="text-lg font-bold text-zinc-800 dark:text-white mb-4 flex items-center gap-2">
                <i class="fa fa-image text-purple-500"></i>
                Descripción y Foto
            </h3>
            <div class="space-y-4">
                {{-- Descripción --}}
                <div>
                    <label for="descripcion"
                        class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200 mb-1">Descripción</label>
                    <textarea name="descripcion" id="descripcion" rows="6"
                        placeholder="Características, especificaciones técnicas, compatibilidad..."
                        class="w-full px-3 py-2 text-sm border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none">{{ old('descripcion', $repuesto->descripcion ?? '') }}</textarea>
                    @error('descripcion')
                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Foto --}}
                <div>
                    <label for="foto"
                        class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200 mb-1">Fotografía</label>
                    <input type="file" name="foto" id="foto"
                        accept="image/jpeg,image/jpg,image/png,image/webp"
                        class="w-full px-3 py-2 text-sm border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900/40 dark:file:text-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                        JPG, PNG, WEBP • Max 2MB
                    </div>
                    @error('foto')
                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                    @enderror
                    @if (!empty($repuesto->foto))
                        <div class="mt-3 p-3 bg-zinc-50 dark:bg-zinc-700/50 rounded-lg">
                            <p class="text-xs text-zinc-600 dark:text-zinc-400 mb-2">Imagen actual:</p>
                            <img src="{{ asset('storage/' . $repuesto->foto) }}" alt="Foto actual"
                                class="h-24 w-24 object-cover rounded-lg border-2 border-zinc-200 dark:border-zinc-600" />
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modales (mantenidos iguales pero más compactos) -->
    @include('repuestos.partials.modales')

    <!-- Botones de acción -->
    <div class="mt-6 flex justify-end gap-3">
        <a href="{{ route('repuestos.index') }}"
            class="bg-zinc-600 hover:bg-zinc-700 text-white font-semibold px-6 py-2 rounded-lg transition">
            <i class="fa fa-arrow-left"></i> Cancelar
        </a>
        <button type="submit"
            class="bg-zinc-200 dark:bg-zinc-700 hover:bg-zinc-300 dark:hover:bg-zinc-600
            text-zinc-800 dark:text-white font-semibold px-6 py-2 rounded-lg transition">
            <i class="fa fa-save"></i> Guardar Repuesto
        </button>
    </div>
</form>

@push('scripts')
    <script>
        function showModal(id) {
            document.getElementById(id).classList.remove('hidden');
        }

        function hideModal(id) {
            document.getElementById(id).classList.add('hidden');
        }

        $(document).ready(function() {
            // INICIALIZA SELECT2
            $('.select2-modelo, .select2-marca, .select2-ubicacion, .select2-categoria').select2({
                theme: 'default',
                width: '100%'
            });

            // Modelo
            $('.select2-modelo').on('change', function() {
                if ($(this).val() === '__nuevo__') {
                    showModal('modalNuevoModelo');
                }
            });
            $('#btnGuardarModelo').on('click', function() {
                var nuevoModelo = $('#inputNuevoModelo').val();
                if (nuevoModelo.trim() !== '') {
                    var select = $('.select2-modelo');
                    select.append($('<option>', {
                        value: nuevoModelo,
                        text: nuevoModelo,
                        selected: true
                    }));
                    select.val(nuevoModelo).trigger('change');
                    hideModal('modalNuevoModelo');
                    $('#inputNuevoModelo').val('');
                }
            });

            // Marca
            $('.select2-marca').on('change', function() {
                if ($(this).val() === '__nuevo__') {
                    showModal('modalNuevaMarca');
                }
            });
            $('#btnGuardarMarca').on('click', function() {
                var nuevaMarca = $('#inputNuevaMarca').val();
                if (nuevaMarca.trim() !== '') {
                    var select = $('.select2-marca');
                    select.append($('<option>', {
                        value: nuevaMarca,
                        text: nuevaMarca,
                        selected: true
                    }));
                    select.val(nuevaMarca).trigger('change');
                    hideModal('modalNuevaMarca');
                    $('#inputNuevaMarca').val('');
                }
            });

            // Ubicación
            $('.select2-ubicacion').on('change', function() {
                if ($(this).val() === '__nuevo__') {
                    showModal('modalNuevaUbicacion');
                }
            });
            $('#btnGuardarUbicacion').on('click', function() {
                var nuevaUbicacion = $('#inputNuevaUbicacion').val();
                if (nuevaUbicacion.trim() !== '') {
                    var select = $('.select2-ubicacion');
                    select.append($('<option>', {
                        value: nuevaUbicacion,
                        text: nuevaUbicacion,
                        selected: true
                    }));
                    select.val(nuevaUbicacion).trigger('change');
                    hideModal('modalNuevaUbicacion');
                    $('#inputNuevaUbicacion').val('');
                }
            });

            // CATEGORÍA
            $('.select2-categoria').on('change', function() {
                if ($(this).val() === '__nuevo__') {
                    showModal('modalNuevaCategoria');
                }
            });
            $('#btnGuardarCategoria').on('click', function() {
                var nuevaCategoria = $('#inputNuevaCategoria').val();
                if (nuevaCategoria.trim() !== '') {
                    $.ajax({
                        url: '{{ route('categorias.ajax-store') }}',
                        method: 'POST',
                        data: {
                            nombre: nuevaCategoria,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            var select = $('.select2-categoria');
                            var newOption = new Option(response.nombre, response.id, true,
                                true);
                            select.append(newOption).trigger('change');
                            hideModal('modalNuevaCategoria');
                            $('#inputNuevaCategoria').val('');
                        },
                        error: function(xhr) {
                            alert('No se pudo agregar la categoría: ' + xhr.responseJSON
                                .message);
                        }
                    });
                }
            });

            // Botón cancelar para todos los modales
            $('.btn-cancel-modal').on('click', function() {
                hideModal('modalNuevoModelo');
                hideModal('modalNuevaMarca');
                hideModal('modalNuevaUbicacion');
                hideModal('modalNuevaCategoria');
            });
        });
    </script>
@endpush
