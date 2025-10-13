{{-- Modal Modelo --}}
<div id="modalNuevoModelo" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div
        class="bg-white dark:bg-zinc-800 rounded-lg shadow-xl w-full max-w-md p-6 border border-zinc-200 dark:border-zinc-700">
        <h3 class="text-lg font-bold mb-4 text-zinc-800 dark:text-white flex items-center gap-2">
            <i class="fa fa-plus-circle text-blue-500"></i>
            Agregar nuevo modelo
        </h3>
        <input type="text" id="inputNuevoModelo"
            class="w-full px-4 py-2 text-sm border border-zinc-300 dark:border-zinc-600 rounded-lg mb-4 bg-white dark:bg-zinc-700 text-zinc-800 dark:text-white"
            placeholder="Nombre del modelo">
        <div class="flex justify-end gap-2">
            <button type="button"
                class="btn-cancel-modal bg-zinc-600 hover:bg-zinc-700 px-4 py-2 rounded-lg text-white text-sm font-semibold transition">Cancelar</button>
            <button type="button" id="btnGuardarModelo"
                class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg text-white text-sm font-semibold transition">Guardar</button>
        </div>
    </div>
</div>

{{-- Modal Marca --}}
<div id="modalNuevaMarca" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div
        class="bg-white dark:bg-zinc-800 rounded-lg shadow-xl w-full max-w-md p-6 border border-zinc-200 dark:border-zinc-700">
        <h3 class="text-lg font-bold mb-4 text-zinc-800 dark:text-white flex items-center gap-2">
            <i class="fa fa-plus-circle text-blue-500"></i>
            Agregar nueva marca
        </h3>
        <input type="text" id="inputNuevaMarca"
            class="w-full px-4 py-2 text-sm border border-zinc-300 dark:border-zinc-600 rounded-lg mb-4 bg-white dark:bg-zinc-700 text-zinc-800 dark:text-white"
            placeholder="Nombre de la marca">
        <div class="flex justify-end gap-2">
            <button type="button"
                class="btn-cancel-modal bg-zinc-600 hover:bg-zinc-700 px-4 py-2 rounded-lg text-white text-sm font-semibold transition">Cancelar</button>
            <button type="button" id="btnGuardarMarca"
                class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg text-white text-sm font-semibold transition">Guardar</button>
        </div>
    </div>
</div>

{{-- Modal Ubicación --}}
<div id="modalNuevaUbicacion" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div
        class="bg-white dark:bg-zinc-800 rounded-lg shadow-xl w-full max-w-md p-6 border border-zinc-200 dark:border-zinc-700">
        <h3 class="text-lg font-bold mb-4 text-zinc-800 dark:text-white flex items-center gap-2">
            <i class="fa fa-plus-circle text-blue-500"></i>
            Agregar nueva ubicación
        </h3>
        <input type="text" id="inputNuevaUbicacion"
            class="w-full px-4 py-2 text-sm border border-zinc-300 dark:border-zinc-600 rounded-lg mb-4 bg-white dark:bg-zinc-700 text-zinc-800 dark:text-white"
            placeholder="Nombre de la ubicación">
        <div class="flex justify-end gap-2">
            <button type="button"
                class="btn-cancel-modal bg-zinc-600 hover:bg-zinc-700 px-4 py-2 rounded-lg text-white text-sm font-semibold transition">Cancelar</button>
            <button type="button" id="btnGuardarUbicacion"
                class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg text-white text-sm font-semibold transition">Guardar</button>
        </div>
    </div>
</div>

{{-- Modal Categoría --}}
<div id="modalNuevaCategoria" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div
        class="bg-white dark:bg-zinc-800 rounded-lg shadow-xl w-full max-w-md p-6 border border-zinc-200 dark:border-zinc-700">
        <h3 class="text-lg font-bold mb-4 text-zinc-800 dark:text-white flex items-center gap-2">
            <i class="fa fa-plus-circle text-blue-500"></i>
            Agregar nueva categoría
        </h3>
        <input type="text" id="inputNuevaCategoria"
            class="w-full px-4 py-2 text-sm border border-zinc-300 dark:border-zinc-600 rounded-lg mb-4 bg-white dark:bg-zinc-700 text-zinc-800 dark:text-white"
            placeholder="Nombre de la categoría">
        <div class="flex justify-end gap-2">
            <button type="button"
                class="btn-cancel-modal bg-zinc-600 hover:bg-zinc-700 px-4 py-2 rounded-lg text-white text-sm font-semibold transition">Cancelar</button>
            <button type="button" id="btnGuardarCategoria"
                class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg text-white text-sm font-semibold transition">Guardar</button>
        </div>
    </div>
</div>
