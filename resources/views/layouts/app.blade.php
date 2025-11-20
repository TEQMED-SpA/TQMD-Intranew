{{-- resources/views/components/layouts/app.blade.php --}}
@props(['title' => config('app.name')])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $title }}</title>

    {{-- Flux: maneja tema claro/oscuro --}}
    @fluxAppearance

    {{-- Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-zinc-100 dark:bg-zinc-950 antialiased">
    {{-- Contenido de la página --}}
    {{ $slot }}

    {{-- Stacks / scripts --}}
    @stack('scripts')
    @livewireScripts
    @fluxScripts
</body>

</html>
