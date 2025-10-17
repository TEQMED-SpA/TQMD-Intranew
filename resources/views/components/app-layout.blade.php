@props(['title' => config('app.name')])

<x-layouts.app :title="$title">
    @isset($header)
        <div class="px-4 sm:px-6 lg:px-8 py-4">
            {{ $header }}
        </div>
    @endisset

    {{ $slot }}
</x-layouts.app>
