<form method="POST" action="{{ route('repuestos.store') }}" enctype="multipart/form-data" class="max-w-7xl mx-auto">
    @csrf

    {{-- Resumen de errores --}}
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
        {{-- Columna 1: Información Básica --}}
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-md p-6 border border-zinc-200 dark:border-zinc-700">
            <h3 class="text-lg font-bold text-zinc-800 dark:text-white mb-4 flex items-center gap-2">
                <i class="fa fa-info-circle text-blue-500"></i> Información Básica
            </h3>

            <div class="space-y-4">
                {{-- Serie (required) --}}
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

                {{-- Nombre (required) --}}
                <div>
                    <label for="nombre"
                        class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200 mb-1">Nombre del
                        Repuesto</label>
                    <input type="text" name="nombre" id="nombre" placeholder="Ej: Bomba de concentrado"
                        value="{{ old('nombre', $repuesto->nombre ?? '') }}"
                        class="w-full px-3 py-2 text-sm border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                    @error('nombre')
                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Modelo (required) --}}
                <div>
                    <label for="modelo"
                        class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200 mb-1">Modelo</label>
                    <select name="modelo" id="modelo"
                        class="select2-modelo w-full px-3 py-2 text-sm border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
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

                {{-- Marca (required) --}}
                <div>
                    <label for="marca"
                        class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200 mb-1">Marca</label>
                    <select name="marca" id="marca"
                        class="select2-marca w-full px-3 py-2 text-sm border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
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

        {{-- Columna 2: Stock y Ubicación --}}
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-md p-6 border border-zinc-200 dark:border-zinc-700">
            <h3 class="text-lg font-bold text-zinc-800 dark:text-white mb-4 flex items-center gap-2">
                <i class="fa fa-cube text-green-500"></i> Stock y Ubicación
            </h3>

            <div class="space-y-4">
                {{-- Stock (required) --}}
                <div>
                    <label for="stock"
                        class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200 mb-1">Cantidad en
                        Stock</label>
                    <input type="number" name="stock" id="stock" min="0" step="1"
                        placeholder="Ej: 25" value="{{ old('stock', $repuesto->stock ?? '') }}"
                        class="w-full px-3 py-2 text-sm border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                    @error('stock')
                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Ubicación (required) --}}
                <div>
                    <label for="ubicacion"
                        class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200 mb-1">Ubicación en
                        Almacén</label>
                    <select name="ubicacion" id="ubicacion"
                        class="select2-ubicacion w-full px-3 py-2 text-sm border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
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

                {{-- Categoría (required y corrige value -> id) --}}
                <div>
                    <label for="categoria_id"
                        class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200 mb-1">Categoría</label>
                    <select name="categoria_id" id="categoria_id"
                        class="select2-categoria w-full px-3 py-2 text-sm border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                        <option value="" disabled
                            {{ !old('categoria_id', $repuesto->categoria_id ?? '') ? 'selected' : '' }}>Seleccionar...
                        </option>
                        @foreach ($categorias as $categoria)
                            <option value="{{ $categoria->id }}" @selected(old('categoria_id', $repuesto->categoria_id ?? '') == $categoria->id)>
                                {{ $categoria->nombre }}
                            </option>
                        @endforeach
                        <option value="__nuevo__">➕ Agregar nueva...</option>
                    </select>
                    @error('categoria_id')
                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Estado (opcional) --}}
                {{-- Estado (required) --}}
                <div>
                    <label for="estado_id"
                        class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200 mb-1">Estado</label>
                    <select name="estado_id" id="estado_id"
                        class="w-full px-3 py-2 text-sm border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                        <option value="" disabled
                            {{ !old('estado_id', $repuesto->estado_id ?? '') ? 'selected' : '' }}>Seleccionar...
                        </option>
                        @foreach ($estados as $estado)
                            <option value="{{ $estado->id }}" @selected(old('estado_id', $repuesto->estado_id ?? '') == $estado->id)>
                                {{ ucfirst($estado->nombre) }}</option>
                        @endforeach
                    </select>
                    @error('estado_id')
                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Columna 3: Descripción y Foto --}}
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-md p-6 border border-zinc-200 dark:border-zinc-700">
            <h3 class="text-lg font-bold text-zinc-800 dark:text-white mb-4 flex items-center gap-2">
                <i class="fa fa-image text-purple-500"></i> Descripción y Foto
            </h3>

            <div class="space-y-4">
                {{-- Descripción (opcional) --}}
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

                {{-- Foto (opcional) --}}
                <div>
                    <label for="foto"
                        class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200 mb-1">Fotografía</label>
                    <input type="file" name="foto" id="foto"
                        accept="image/jpeg,image/jpg,image/png,image/webp"
                        class="w-full px-3 py-2 text-sm border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900/40 dark:file:text-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">JPG, PNG, WEBP • Max 2MB</div>
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

    {{-- Modales --}}
    @include('repuestos.partials.modales')

    {{-- Acciones --}}
    <div class="mt-6 flex justify-end gap-3">
        <a href="{{ route('repuestos.index') }}"
            class="bg-zinc-600 hover:bg-zinc-700 text-white font-semibold px-6 py-2 rounded-lg transition">
            <i class="fa fa-arrow-left"></i> Cancelar
        </a>
        <button type="submit"
            class="bg-zinc-200 dark:bg-zinc-700 hover:bg-zinc-300 dark:hover:bg-zinc-600 text-zinc-800 dark:text-white font-semibold px-6 py-2 rounded-lg transition">
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

        $(function() {
            // INICIALIZA SELECT2
            $('.select2-modelo, .select2-marca, .select2-ubicacion, .select2-categoria').select2({
                theme: 'default',
                width: '100%'
            });

            // Si el usuario elige "__nuevo__", abre modal; si lo cancela, vuelve a vacío
            function guardarrailNuevo(selector, modalId, inputId) {
                $(selector).on('change', function() {
                    if ($(this).val() === '__nuevo__') showModal(modalId);
                });
                $(`#${modalId} .btn-cancel-modal`).on('click', function() {
                    $(selector).val('').trigger('change');
                });
                $(`#btnGuardar${inputId}`).on('click', function() {
                    var nuevo = $(`#input${inputId}`).val().trim();
                    if (nuevo !== '') {
                        var select = $(selector);
                        select.append(new Option(nuevo, nuevo, true, true));
                        select.val(nuevo).trigger('change');
                        hideModal(modalId);
                        $(`#input${inputId}`).val('');
                    }
                });
            }

            guardarrailNuevo('.select2-modelo', 'modalNuevoModelo', 'NuevoModelo');
            guardarrailNuevo('.select2-marca', 'modalNuevaMarca', 'NuevaMarca');
            guardarrailNuevo('.select2-ubicacion', 'modalNuevaUbicacion', 'NuevaUbicacion');

            // Categoría con AJAX de creación
            $('.select2-categoria').on('change', function() {
                if ($(this).val() === '__nuevo__') showModal('modalNuevaCategoria');
            });
            $('#btnGuardarCategoria').on('click', function() {
                var nuevaCategoria = $('#inputNuevaCategoria').val().trim();
                if (nuevaCategoria !== '') {
                    $.post('{{ route('categorias.ajax-store') }}', {
                        nombre: nuevaCategoria,
                        _token: '{{ csrf_token() }}'
                    }).done(function(response) {
                        var select = $('.select2-categoria');
                        var newOption = new Option(response.nombre, response.id, true, true);
                        select.append(newOption).trigger('change');
                        hideModal('modalNuevaCategoria');
                        $('#inputNuevaCategoria').val('');
                    }).fail(function(xhr) {
                        alert('No se pudo agregar la categoría: ' + (xhr.responseJSON?.message ||
                            'Error'));
                    });
                }
            });
            $('.btn-cancel-modal').on('click', function() {
                hideModal('modalNuevoModelo');
                hideModal('modalNuevaMarca');
                hideModal('modalNuevaUbicacion');
                hideModal('modalNuevaCategoria');
            });
        });
    </script>
@endpush
