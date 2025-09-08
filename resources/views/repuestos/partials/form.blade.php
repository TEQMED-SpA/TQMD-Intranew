<form method="POST" action="{{ route('repuestos.store') }}" enctype="multipart/form-data" class="max-w-4xl mx-auto">
    @csrf
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Columna 1 -->
        <div class="space-y-4">
            {{-- Serie --}}
            <div>
                <label for="producto_serie" class="block text-zinc-700 dark:text-zinc-200 font-medium mb-2">Serie</label>
                <input type="text" name="producto_serie" id="producto_serie" placeholder="Ej: ABC123-456"
                    value="{{ old('producto_serie', $repuesto->producto_serie ?? '') }}"
                    class="w-full px-4 py-3 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                @error('producto_serie')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            {{-- Nombre --}}
            <div>
                <label for="producto_nombre" class="block text-zinc-700 dark:text-zinc-200 font-medium mb-2">Nombre del
                    Repuesto</label>
                <input type="text" name="producto_nombre" id="producto_nombre" placeholder="Ej: Bomba de concentrado"
                    value="{{ old('producto_nombre', $repuesto->producto_nombre ?? '') }}"
                    class="w-full px-4 py-3 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                @error('producto_nombre')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            {{-- Modelo --}}
            <div>
                <label for="producto_modelo"
                    class="block text-zinc-700 dark:text-zinc-200 font-medium mb-2">Modelo</label>
                <select name="producto_modelo" id="producto_modelo"
                    class="select2-modelo w-full px-4 py-3 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                    <option value="">Seleccionar modelo...</option>
                    @foreach ($modelos as $modelo)
                        <option value="{{ $modelo }}">{{ $modelo }}</option>
                    @endforeach
                    <option value="__nuevo__">Agregar nuevo modelo...</option>
                </select>
                @error('producto_modelo')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            {{-- Marca --}}
            <div>
                <label for="producto_marca"
                    class="block text-zinc-700 dark:text-zinc-200 font-medium mb-2">Marca</label>
                <select name="producto_marca" id="producto_marca"
                    class="select2-marca w-full px-4 py-3 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                    <option value="">Seleccionar marca...</option>
                    @foreach ($marcas as $marca)
                        <option value="{{ $marca }}">{{ $marca }}</option>
                    @endforeach
                    <option value="__nuevo__">Agregar nueva marca...</option>
                </select>
                @error('producto_marca')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            {{-- Stock --}}
            <div>
                <label for="producto_stock" class="block text-zinc-700 dark:text-zinc-200 font-medium mb-2">Cantidad en
                    Stock</label>
                <input type="number" name="producto_stock" id="producto_stock" min="0" step="1"
                    placeholder="Ej: 25" value="{{ old('producto_stock', $repuesto->producto_stock ?? '') }}"
                    class="w-full px-4 py-3 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                @error('producto_stock')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Columna 2 -->
        <div class="space-y-4">
            {{-- Ubicación --}}
            <div>
                <label for="producto_ubicacion"
                    class="block text-zinc-700 dark:text-zinc-200 font-medium mb-2">Ubicación en Almacén</label>
                <select name="producto_ubicacion" id="producto_ubicacion"
                    class="select2-ubicacion w-full px-4 py-3 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                    <option value="">Seleccionar ubicación...</option>
                    @foreach ($ubicaciones as $ubicacion)
                        <option value="{{ $ubicacion }}">{{ $ubicacion }}</option>
                    @endforeach
                    <option value="__nuevo__">Agregar nueva ubicación...</option>
                </select>
                @error('producto_ubicacion')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            {{-- Categoría --}}
            <div>
                <label for="categoria_id"
                    class="block text-zinc-700 dark:text-zinc-200 font-medium mb-2">Categoría</label>
                <select name="categoria_id" id="categoria_id" required
                    class="select2-categoria w-full px-4 py-3 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                    <option value="" disabled
                        {{ old('categoria_id', $repuesto->categoria_id ?? '') ? '' : 'selected' }}>Seleccionar
                        categoría...</option>
                    @foreach ($categorias as $categoria)
                        <option value="{{ $categoria->categoria_id }}" @selected(old('categoria_id', $repuesto->categoria_id ?? '') == $categoria->categoria_id)>
                            {{ $categoria->categoria_nombre }}
                        </option>
                    @endforeach
                    <option value="__nuevo__">Agregar nueva categoría...</option>
                </select>
                @error('categoria_id')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            {{-- Estado --}}
            <div>
                <label for="producto_estado"
                    class="block text-zinc-700 dark:text-zinc-200 font-medium mb-2">Estado</label>
                <select name="producto_estado" id="producto_estado"
                    class="w-full px-4 py-3 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                    <option value="activo" @selected(old('producto_estado', $repuesto->producto_estado ?? '') === 'activo')>✅ Activo</option>
                    <option value="inactivo" @selected(old('producto_estado', $repuesto->producto_estado ?? '') === 'inactivo')>❌ Inactivo</option>
                </select>
                @error('producto_estado')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            {{-- Foto --}}
            <div>
                <label for="producto_foto" class="block text-zinc-700 dark:text-zinc-200 font-medium mb-2">Fotografía
                    del Repuesto</label>
                <input type="file" name="producto_foto" id="producto_foto"
                    accept="image/jpeg,image/jpg,image/png,image/webp"
                    class="w-full px-4 py-3 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                <div class="text-xs text-zinc-500 mt-1">
                    📁 Formatos permitidos: JPG, JPEG, PNG, WEBP | Tamaño máximo: 2MB | Dimensiones recomendadas:
                    800x600px
                </div>
                @error('producto_foto')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror
                @if (!empty($repuesto->producto_foto))
                    <div class="mt-3 p-3 bg-zinc-50 dark:bg-zinc-800 rounded-lg">
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-2">Imagen actual:</p>
                        <img src="{{ asset('storage/' . $repuesto->producto_foto) }}" alt="Foto actual del repuesto"
                            class="h-20 w-20 object-cover rounded-lg border-2 border-zinc-200 dark:border-zinc-600" />
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Descripción - Campo completo ancho -->
    <div class="mt-6">
        <label for="producto_descripcion" class="block text-zinc-700 dark:text-zinc-200 font-medium mb-2">Descripción
            Detallada</label>
        <textarea name="producto_descripcion" id="producto_descripcion" rows="3"
            placeholder="Describe las características, especificaciones técnicas, compatibilidad, etc..."
            class="w-full px-4 py-3 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all resize-none">{{ old('producto_descripcion', $repuesto->producto_descripcion ?? '') }}</textarea>
        @error('producto_descripcion')
            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
        @enderror
    </div>

    <!-- Modales para agregar nuevo modelo/marca/ubicación/categoría -->
    <!-- Modal Modelo -->
    <div id="modalNuevoModelo"
        class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
        <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-lg w-full max-w-sm p-6">
            <h3 class="text-lg font-bold mb-4 text-zinc-800 dark:text-white">Agregar nuevo modelo</h3>
            <input type="text" id="inputNuevoModelo"
                class="w-full px-4 py-2 border rounded mb-4 bg-zinc-50 dark:bg-zinc-800 text-zinc-800 dark:text-white"
                placeholder="Nombre del modelo">
            <div class="flex justify-end gap-2">
                <button type="button"
                    class="btn-cancel-modal bg-zinc-200 dark:bg-zinc-700 px-3 py-2 rounded text-zinc-800 dark:text-white">Cancelar</button>
                <button type="button" id="btnGuardarModelo"
                    class="bg-blue-600 hover:bg-blue-700 px-3 py-2 rounded text-white font-bold">Guardar</button>
            </div>
        </div>
    </div>
    <!-- Modal Marca -->
    <div id="modalNuevaMarca"
        class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
        <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-lg w-full max-w-sm p-6">
            <h3 class="text-lg font-bold mb-4 text-zinc-800 dark:text-white">Agregar nueva marca</h3>
            <input type="text" id="inputNuevaMarca"
                class="w-full px-4 py-2 border rounded mb-4 bg-zinc-50 dark:bg-zinc-800 text-zinc-800 dark:text-white"
                placeholder="Nombre de la marca">
            <div class="flex justify-end gap-2">
                <button type="button"
                    class="btn-cancel-modal bg-zinc-200 dark:bg-zinc-700 px-3 py-2 rounded text-zinc-800 dark:text-white">Cancelar</button>
                <button type="button" id="btnGuardarMarca"
                    class="bg-blue-600 hover:bg-blue-700 px-3 py-2 rounded text-white font-bold">Guardar</button>
            </div>
        </div>
    </div>
    <!-- Modal Ubicación -->
    <div id="modalNuevaUbicacion"
        class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
        <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-lg w-full max-w-sm p-6">
            <h3 class="text-lg font-bold mb-4 text-zinc-800 dark:text-white">Agregar nueva ubicación</h3>
            <input type="text" id="inputNuevaUbicacion"
                class="w-full px-4 py-2 border rounded mb-4 bg-zinc-50 dark:bg-zinc-800 text-zinc-800 dark:text-white"
                placeholder="Nombre de la ubicación">
            <div class="flex justify-end gap-2">
                <button type="button"
                    class="btn-cancel-modal bg-zinc-200 dark:bg-zinc-700 px-3 py-2 rounded text-zinc-800 dark:text-white">Cancelar</button>
                <button type="button" id="btnGuardarUbicacion"
                    class="bg-blue-600 hover:bg-blue-700 px-3 py-2 rounded text-white font-bold">Guardar</button>
            </div>
        </div>
    </div>
    <!-- Modal Categoría -->
    <div id="modalNuevaCategoria"
        class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
        <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-lg w-full max-w-sm p-6">
            <h3 class="text-lg font-bold mb-4 text-zinc-800 dark:text-white">Agregar nueva categoría</h3>
            <input type="text" id="inputNuevaCategoria"
                class="w-full px-4 py-2 border rounded mb-4 bg-zinc-50 dark:bg-zinc-800 text-zinc-800 dark:text-white"
                placeholder="Nombre de la categoría">
            <div class="flex justify-end gap-2">
                <button type="button"
                    class="btn-cancel-modal bg-zinc-200 dark:bg-zinc-700 px-3 py-2 rounded text-zinc-800 dark:text-white">Cancelar</button>
                <button type="button" id="btnGuardarCategoria"
                    class="bg-blue-600 hover:bg-blue-700 px-3 py-2 rounded text-white font-bold">Guardar</button>
            </div>
        </div>
    </div>

    <div class="mt-8 flex justify-end">
        <button type="submit"
            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg shadow">Guardar
            repuesto</button>
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
            // INICIALIZA SELECT2 EN TODOS LOS SELECTS
            $('.select2-modelo').select2();
            $('.select2-marca').select2();
            $('.select2-ubicacion').select2();
            $('.select2-categoria').select2();

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
                            categoria_nombre: nuevaCategoria,
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
