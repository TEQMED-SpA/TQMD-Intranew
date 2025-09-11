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
                        <span class="text-zinc-900 dark:text-zinc-100">{{ $repuesto->serie }}</span>
                    </li>
                    <li class="py-3 flex justify-between items-center">
                        <span class="font-semibold text-zinc-700 dark:text-zinc-200">Nombre:</span>
                        <span class="text-zinc-900 dark:text-zinc-100">{{ $repuesto->nombre }}</span>
                    </li>
                    <li class="py-3 flex justify-between items-center">
                        <span class="font-semibold text-zinc-700 dark:text-zinc-200">Modelo:</span>
                        <span class="text-zinc-900 dark:text-zinc-100">{{ $repuesto->modelo }}</span>
                    </li>
                    <li class="py-3 flex justify-between items-center">
                        <span class="font-semibold text-zinc-700 dark:text-zinc-200">Marca:</span>
                        <span class="text-zinc-900 dark:text-zinc-100">{{ $repuesto->marca }}</span>
                    </li>
                    <li class="py-3 flex justify-between items-center">
                        <span class="font-semibold text-zinc-700 dark:text-zinc-200">Ubicación:</span>
                        <span class="text-zinc-900 dark:text-zinc-100">{{ $repuesto->ubicacion }}</span>
                    </li>
                    <li class="py-3 flex justify-between items-center">
                        <span class="font-semibold text-zinc-700 dark:text-zinc-200">Descripción:</span>
                        <span class="text-zinc-900 dark:text-zinc-100">{{ $repuesto->descripcion }}</span>
                    </li>
                    <li class="py-3 flex justify-between items-center">
                        <span class="font-semibold text-zinc-700 dark:text-zinc-200">Stock:</span>
                        <span class="text-zinc-900 dark:text-zinc-100">{{ $repuesto->stock }}</span>
                    </li>
                    <li class="py-3 flex justify-between items-center">
                        <span class="font-semibold text-zinc-700 dark:text-zinc-200">Foto:</span>
                        <span x-data="imageViewer('{{ $repuesto->foto ? asset('storage/' . $repuesto->foto) : null }}')" class="relative">
                            @if ($repuesto->foto)
                                <!-- Botón para abrir el modal -->
                                <button @click="openModal" type="button"
                                    class="inline-flex items-center rounded-lg px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 dark:from-blue-600 dark:to-blue-700 dark:hover:from-blue-700 dark:hover:to-blue-800 text-white text-sm font-medium gap-2 transform hover:scale-105 active:scale-95 transition-all duration-200 ease-in-out shadow-md hover:shadow-lg"
                                    aria-label="Ver imagen del repuesto">
                                    <i class="fa fa-eye" aria-hidden="true"></i>
                                    Ver imagen
                                </button>

                                <!-- Modal de visualización -->
                                <div x-show="showModal" x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-200"
                                    x-transition:leave-start="opacity-100 scale-100"
                                    x-transition:leave-end="opacity-0 scale-95"
                                    class="fixed inset-0 z-50 flex items-center justify-center p-4"
                                    style="background: rgba(0,0,0,0.7);" @click.self="closeModal"
                                    @keydown.escape.window="closeModal" role="dialog" aria-modal="true"
                                    aria-labelledby="image-modal-title">

                                    <!-- Marco del modal -->
                                    <div class="relative bg-white dark:bg-zinc-900 rounded-xl shadow-2xl border border-zinc-200 dark:border-zinc-700 p-4 w-auto max-w-md flex flex-col items-center"
                                        @click.stop>

                                        <!-- Botón cerrar -->
                                        <button @click="closeModal"
                                            class="absolute top-2 right-2 text-zinc-600 dark:text-zinc-300 hover:text-zinc-900 dark:hover:text-zinc-100 bg-zinc-200 dark:bg-zinc-700 rounded-full p-2 transition"
                                            aria-label="Cerrar modal">
                                            <i class="fa fa-times" aria-hidden="true"></i>
                                        </button>

                                        <h3 id="image-modal-title"
                                            class="text-base font-semibold text-zinc-800 dark:text-zinc-100 mb-4">
                                            Imagen del repuesto
                                        </h3>

                                        <!-- Contenedor de imagen con zoom y drag -->
                                        <div x-ref="imgContainer" x-init="initContainer"
                                            class="overflow-hidden border-2 border-zinc-200 dark:border-zinc-700 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center select-none relative"
                                            style="width: 100%; height: 350px; max-height: 60vh;" @mousedown="startDrag"
                                            @mousemove="moveDrag" @mouseup="endDrag" @mouseleave="endDrag"
                                            @touchstart="startDrag" @touchmove="moveDrag" @touchend="endDrag"
                                            @wheel.prevent="handleWheel">

                                            <img :style="imageStyle" :src="imageUrl" alt="Foto del repuesto"
                                                class="max-h-full max-w-full object-contain rounded-lg"
                                                @load="onImgLoad" draggable="false" />

                                            <div x-show="zoom > 1"
                                                class="absolute top-3 right-3 bg-blue-500 text-white text-xs px-2 py-1 rounded-full"
                                                x-text="`${Math.round(zoom * 100)}%`">
                                            </div>
                                        </div>

                                        <!-- Controles de zoom -->
                                        <div class="flex flex-wrap gap-2 mt-5 justify-center">
                                            <button @click="zoomIn"
                                                class="bg-blue-500 hover:bg-blue-600 text-white rounded-full px-3 py-1 text-sm flex items-center gap-1 disabled:opacity-50 disabled:cursor-not-allowed"
                                                :disabled="zoom >= maxZoom" aria-label="Aumentar zoom">
                                                <i class="fa fa-search-plus" aria-hidden="true"></i>
                                                <span>Zoom +</span>
                                            </button>
                                            <button @click="zoomOut"
                                                class="bg-blue-500 hover:bg-blue-600 text-white rounded-full px-3 py-1 text-sm flex items-center gap-1 disabled:opacity-50 disabled:cursor-not-allowed"
                                                :disabled="zoom <= minZoom" aria-label="Reducir zoom">
                                                <i class="fa fa-search-minus" aria-hidden="true"></i>
                                                <span>Zoom -</span>
                                            </button>
                                            <button @click="zoomReset"
                                                class="bg-gray-300 hover:bg-gray-400 text-zinc-800 rounded-full px-3 py-1 text-sm flex items-center gap-1"
                                                aria-label="Restablecer zoom">
                                                <i class="fa fa-compress" aria-hidden="true"></i>
                                                <span>Reset</span>
                                            </button>
                                            <button @click="zoomToFit"
                                                class="bg-green-500 hover:bg-green-600 text-white rounded-full px-3 py-1 text-sm flex items-center gap-1"
                                                aria-label="Ajustar a pantalla">
                                                <i class="fa fa-expand" aria-hidden="true"></i>
                                                <span>Ajustar</span>
                                            </button>
                                        </div>

                                        <div class="mt-2 text-xs text-zinc-500 dark:text-zinc-400 text-center">
                                            Usa la <strong>rueda del ratón</strong> para hacer zoom, o
                                            <strong>arrastra</strong> la imagen para moverla.<br>
                                            También puedes usar los <strong>botones</strong> para controlar el zoom.
                                        </div>
                                    </div>
                                </div>
                            @else
                                <span class="text-zinc-500 italic">Sin foto</span>
                            @endif
                        </span>
                    </li>
                    <li class="py-3 flex justify-between items-center">
                        <span class="font-semibold text-zinc-700 dark:text-zinc-200">Categoría:</span>
                        <span
                            class="text-zinc-900 dark:text-zinc-100">{{ $repuesto->categoria?->categoria_nombre }}</span>
                    </li>
                    <li class="py-3 flex justify-between items-center">
                        <span class="font-semibold text-zinc-700 dark:text-zinc-200">Estado:</span>
                        @php
                            $isActive = ($repuesto->estado ?? '') === 'Nuevo';
                        @endphp
                        <span
                            class="inline-block rounded-full px-3 py-1 text-xs font-semibold transition-colors duration-200
                            {{ $isActive
                                ? 'bg-green-200 dark:bg-green-800 text-green-900 dark:text-zinc-800'
                                : 'bg-red-200 dark:bg-red-800 text-red-900 dark:text-zinc-800' }}">
                            {{ $isActive ? 'Nuevo' : 'Usado' }}
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
    <script>
        // Define the imageViewer function for Alpine.js
        window.imageViewer = function(imageUrl) {
            return {
                imageUrl,
                showModal: false,
                zoom: 1,
                minZoom: 0.5,
                maxZoom: 3,
                offsetX: 0,
                offsetY: 0,
                dragging: false,
                startX: 0,
                startY: 0,
                imgW: 1,
                imgH: 1,
                containerW: 1,
                containerH: 1,
                startOffsetX: 0,
                startOffsetY: 0,

                get imageStyle() {
                    return `transform: scale(${this.zoom}) translate(${this.offsetX/this.zoom}px, ${this.offsetY/this.zoom}px); 
                        transition: ${this.dragging ? 'none' : 'transform 0.15s'}; 
                        cursor: ${this.zoom > 1 ? 'grab' : 'default'};
                        ${this.dragging && this.zoom > 1 ? 'cursor: grabbing;' : ''}`;
                },

                clamp(val, min, max) {
                    return Math.max(min, Math.min(max, val));
                },

                openModal() {
                    this.showModal = true;
                    this.zoomReset();
                    this.$nextTick(() => {
                        this.initContainer();
                        // Focus trap - focus the close button
                        this.$el.querySelector('button[aria-label="Cerrar modal"]').focus();
                    });
                },

                closeModal() {
                    this.showModal = false;
                    this.zoomReset();
                },

                initContainer() {
                    if (this.$refs.imgContainer) {
                        this.containerW = this.$refs.imgContainer.offsetWidth;
                        this.containerH = this.$refs.imgContainer.offsetHeight;

                        // Add resize observer for responsive behavior
                        if (window.ResizeObserver) {
                            const observer = new ResizeObserver(() => {
                                this.containerW = this.$refs.imgContainer.offsetWidth;
                                this.containerH = this.$refs.imgContainer.offsetHeight;
                                this.adjustOffsets();
                            });
                            observer.observe(this.$refs.imgContainer);
                        }
                    }
                },

                onImgLoad(event) {
                    this.imgW = event.target.naturalWidth;
                    this.imgH = event.target.naturalHeight;
                    this.$nextTick(() => this.zoomToFit());
                },

                startDrag(e) {
                    if (this.zoom === 1) return;
                    this.dragging = true;
                    const clientX = e.type.startsWith('touch') ? e.touches[0].clientX : e.clientX;
                    const clientY = e.type.startsWith('touch') ? e.touches[0].clientY : e.clientY;
                    this.startX = clientX;
                    this.startY = clientY;
                    this.startOffsetX = this.offsetX;
                    this.startOffsetY = this.offsetY;

                    // Change cursor during drag
                    if (this.$refs.imgContainer) {
                        this.$refs.imgContainer.style.cursor = 'grabbing';
                    }
                },

                moveDrag(e) {
                    if (!this.dragging) return;
                    const clientX = e.type.startsWith('touch') ? e.touches[0].clientX : e.clientX;
                    const clientY = e.type.startsWith('touch') ? e.touches[0].clientY : e.clientY;
                    const deltaX = clientX - this.startX;
                    const deltaY = clientY - this.startY;

                    this.updateOffset(this.startOffsetX + deltaX, this.startOffsetY + deltaY);
                },

                endDrag() {
                    this.dragging = false;

                    // Reset cursor after drag
                    if (this.$refs.imgContainer) {
                        this.$refs.imgContainer.style.cursor = this.zoom > 1 ? 'grab' : 'default';
                    }
                },

                updateOffset(newX, newY) {
                    // Calculate bounds based on zoom level and container size
                    const maxOffsetX = Math.max(0, ((this.imgW * this.zoom) - this.containerW) / 2);
                    const maxOffsetY = Math.max(0, ((this.imgH * this.zoom) - this.containerH) / 2);

                    // Limit movement within bounds
                    this.offsetX = this.clamp(newX, -maxOffsetX, maxOffsetX);
                    this.offsetY = this.clamp(newY, -maxOffsetY, maxOffsetY);
                },

                adjustOffsets() {
                    // Recalculate offsets when container or zoom changes
                    this.updateOffset(this.offsetX, this.offsetY);
                },

                handleWheel(e) {
                    // Zoom with mouse wheel
                    const delta = -Math.sign(e.deltaY) * 0.1;
                    const newZoom = this.clamp(this.zoom + delta, this.minZoom, this.maxZoom);

                    if (newZoom !== this.zoom) {
                        // Get mouse position relative to image
                        const rect = this.$refs.imgContainer.getBoundingClientRect();
                        const mouseX = e.clientX - rect.left;
                        const mouseY = e.clientY - rect.top;

                        // Calculate zoom point (center if not over image)
                        const zoomPointX = mouseX - this.containerW / 2;
                        const zoomPointY = mouseY - this.containerH / 2;

                        // Previous distance from zoom point
                        const prevDistX = zoomPointX * this.zoom;
                        const prevDistY = zoomPointY * this.zoom;

                        // Set new zoom
                        this.zoom = newZoom;

                        // New distance from zoom point
                        const newDistX = zoomPointX * this.zoom;
                        const newDistY = zoomPointY * this.zoom;

                        // Adjust offset to zoom into mouse position
                        this.updateOffset(
                            this.offsetX + (newDistX - prevDistX) / this.zoom,
                            this.offsetY + (newDistY - prevDistY) / this.zoom
                        );
                    }
                },

                zoomIn() {
                    const newZoom = this.clamp(this.zoom + 0.25, this.minZoom, this.maxZoom);
                    if (newZoom !== this.zoom) {
                        this.zoom = newZoom;
                        this.adjustOffsets();
                    }
                },

                zoomOut() {
                    const newZoom = this.clamp(this.zoom - 0.25, this.minZoom, this.maxZoom);
                    if (newZoom !== this.zoom) {
                        this.zoom = newZoom;
                        this.adjustOffsets();
                    }
                },

                zoomReset() {
                    this.zoom = 1;
                    this.offsetX = 0;
                    this.offsetY = 0;
                },

                zoomToFit() {
                    if (!this.imgW || !this.imgH || !this.containerW || !this.containerH) return;

                    // Calculate zoom to fit image in container
                    const horizontalRatio = this.containerW / this.imgW;
                    const verticalRatio = this.containerH / this.imgH;

                    // Use the smaller ratio to ensure the image fits entirely
                    let fitZoom = Math.min(horizontalRatio, verticalRatio);

                    // Don't allow zoom greater than 1 for "fit"
                    fitZoom = Math.min(fitZoom, 1);

                    this.zoom = this.clamp(fitZoom, this.minZoom, this.maxZoom);
                    this.offsetX = 0;
                    this.offsetY = 0;
                }
            };
        };
    </script>
</x-layouts.app>
