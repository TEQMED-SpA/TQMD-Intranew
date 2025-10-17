{{-- Bridge: permite que @extends('layouts.app') use tu layout de componentes --}}
<x-layouts.app :title="$title ?? config('app.name')">
    @yield('content')
    @stack('scripts')
</x-layouts.app>
