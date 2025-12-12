<x-layouts.app :title="'Informe Preventivo · DEA'">
    <div class="py-10">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            @include('informes.preventivos.partials.form')
        </div>
    </div>

    @include('informes.preventivos.partials.scripts')
</x-layouts.app>
