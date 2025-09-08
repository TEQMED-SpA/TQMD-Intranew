<x-layouts.app :title="$title ?? 'Editar Repuesto'">
    <div class="min-h-screen px-4 py-8 bg-zinc-50 dark:bg-zinc-800 transition-all">
        <div class="max-w-2xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-zinc-800 dark:text-white">Editar Repuesto</h1>
                <a href="{{ route('repuestos.index') }}">
                    <button type="button"
                        class="bg-zinc-200 dark:bg-zinc-700 text-zinc-100 dark:text-zinc-100 font-semibold px-4 py-2 rounded-lg hover:bg-zinc-300 dark:hover:bg-zinc-600 transition flex items-center gap-2">
                        <i class="fa fa-arrow-left"></i>
                        Volver
                    </button>
                </a>
            </div>
            <div class="rounded-lg shadow bg-white dark:bg-zinc-900 p-6">
                <form action="{{ route('repuestos.update', $repuesto) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    @include('repuestos.form', ['repuesto' => $repuesto, 'categorias' => $categorias])
                    <div class="flex justify-end gap-2">
                        <button type="submit"
                            class="bg-[#00618E] text-white font-semibold px-4 py-2 rounded-lg hover:bg-[#004a6b] transition flex items-center gap-2">
                            <i class="fa fa-check"></i>
                            Actualizar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
