<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}>

<head>
    @include('partials.head')
        @fluxAppearance

    <link rel="icon"
    type="image/x-icon" href="{{ asset('favicon.ico') }}">
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:sidebar sticky collapsible class="bg-zinc-50 dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-700">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <a href="{{ route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
            <x-app-logo />
        </a>

        <flux:navlist.group :heading="__('Bienvenid@') . ' ' . auth()->user()->name" class="grid">
            <flux:navlist.item :content="__('Dashboard')" icon="home" :href="route('dashboard')"
                :current="request()->routeIs('dashboard')" wire:navigate>
                {{ __('Dashboard') }}
            </flux:navlist.item>
        </flux:navlist.group>

        <flux:navlist class="w-64">
            @if (auth()->check())
                {{-- Grupo: Gestión (solo admin) --}}
                @role('admin')
                    <flux:navlist.group heading="Gestión" expandable>
                        <flux:navlist.item icon="users" :href="route('users.index')"
                            :current="request()->routeIs('users.*')" wire:navigate>
                            {{ __('Usuarios') }}
                        </flux:navlist.item>

                        <flux:navlist.item icon="shield-check" :href="route('roles.index')"
                            :current="request()->routeIs('roles.*')" wire:navigate>
                            {{ __('Roles') }}
                        </flux:navlist.item>

                        <flux:navlist.item icon="key" :href="route('privilegios.index')"
                            :current="request()->routeIs('privilegios.*')" wire:navigate>
                            {{ __('Privilegios') }}
                        </flux:navlist.item>
                    </flux:navlist.group>
                @endrole

                {{-- Grupo: Gestión general --}}
                <flux:navlist.group :heading="__('Gestión')" class="grid">
                    <flux:navlist.item icon="ticket" :href="route('tickets.index')"
                        :current="request()->routeIs('tickets.*')" wire:navigate>
                        {{ __('Tickets') }}
                    </flux:navlist.item>

                    <flux:navlist.item icon="users" :href="route('clientes.index')"
                        :current="request()->routeIs('clientes.*')" wire:navigate>
                        {{ __('Clientes') }}
                    </flux:navlist.item>

                    <flux:navlist.item icon="building-office-2" :href="route('centros_medicos.index')"
                        :current="request()->routeIs('centros_medicos.*')" wire:navigate>
                        {{ __('Centros Médicos') }}
                    </flux:navlist.item>

                    @php
                        $u = auth()->user();

                        // Condición robusta: si tienes spatie, intenta roles conocidos;
                        // si no, o si hay diferencias de nombre/acentos, cae en autenticado.
                        $tieneRolEquipos = false;
                        if ($u && method_exists($u, 'hasAnyRole')) {
                            $tieneRolEquipos = $u->hasAnyRole([
                                'admin',
                                'auditor',
                                'tecnico',
                                'Administrador',
                                'Auditor',
                                'Técnico',
                            ]);
                        } elseif ($u && method_exists($u, 'hasRole')) {
                            $tieneRolEquipos = $u->hasRole('admin') || $u->hasRole('auditor') || $u->hasRole('tecnico');
                        }

                        // Si nada de lo anterior funciona, al menos muéstralo a autenticados
                        $mostrarEquipos = $tieneRolEquipos || auth()->check();
                    @endphp

                    @if ($mostrarEquipos)
                        <flux:navlist.item icon="cpu-chip" :href="route('equipos.index')"
                            :current="request()->routeIs('equipos.*')" wire:navigate>
                            {{ __('Máquinas') }}
                        </flux:navlist.item>
                    @endif
                </flux:navlist.group>

                @php
                    $u = auth()->user();

                    // Mostrar grupo Inventario si tiene alguno de estos privilegios
                    $mostrarInventario =
                        $u && method_exists($u, 'hasAnyPrivilege')
                            ? $u->hasAnyPrivilege([
                                'ver_repuestos',
                                'editar_repuestos',
                                'ver_solicitudes',
                                'ver_inventario_tecnico', // agregado
                                'gestionar_inventario_tecnico', // agregado
                            ])
                            : false;

                    // Fallback por rol si no existe hasAnyPrivilege en el usuario
                    if (!$mostrarInventario && $u && method_exists($u, 'hasRole')) {
                        $mostrarInventario = $u->hasRole('tecnico') || $u->hasRole('auditor') || $u->hasRole('admin');
                    }
                @endphp

                @if ($mostrarInventario)
                    {{-- Grupo: Inventario (según privilegios) --}}
                    <flux:navlist.group :heading="__('Inventario')" class="grid">
                        @privilegio('ver_repuestos')
                            <flux:navlist.item icon="archive-box" :href="route('repuestos.index')"
                                :current="request()->routeIs('repuestos.*')" wire:navigate>
                                {{ __('Repuestos') }}
                            </flux:navlist.item>
                        @endprivilegio

                        {{-- Inventario Técnicos: NO depender de ver_repuestos --}}
                        @php
                            $puedeVerInvTecnico =
                                $u && method_exists($u, 'hasAnyPrivilege')
                                    ? $u->hasAnyPrivilege(['ver_inventario_tecnico', 'gestionar_inventario_tecnico'])
                                    : method_exists($u, 'hasRole') &&
                                        ($u->hasRole('tecnico') || $u->hasRole('auditor') || $u->hasRole('admin'));
                        @endphp
                        @if ($puedeVerInvTecnico)
                            <flux:navlist.item icon="archive-box-arrow-down" :href="route('invtecnico.index')"
                                :current="request()->routeIs('invtecnico.*')" wire:navigate>
                                {{ __('Inventario') }}
                            </flux:navlist.item>
                        @endif

                        @privilegio('ver_repuestos')
                            <flux:navlist.item icon="tag" :href="route('categorias.index')"
                                :current="request()->routeIs('categorias.*')" wire:navigate>
                                {{ __('Categorías') }}
                            </flux:navlist.item>
                        @endprivilegio

                        <flux:navlist.item icon="arrow-right-start-on-rectangle" :href="route('salidas.index')"
                            :current="request()->routeIs('salidas.*')" wire:navigate>
                            {{ __('Salidas') }}
                        </flux:navlist.item>

                        @privilegio('ver_solicitudes')
                            <flux:navlist.item icon="clipboard-document-list" :href="route('solicitudes.index')"
                                :current="request()->routeIs('solicitudes.*')" wire:navigate>
                                {{ __('Solicitudes') }}
                            </flux:navlist.item>
                        @endprivilegio
                    </flux:navlist.group>
                @endif

                {{-- Grupo: Utilidades --}}
                <flux:navlist.group :heading="__('Utilidades')" class="grid">
                    <flux:navlist.item icon="document-text" href="{{ route('informes.index') }}"
                        :current="request()->routeIs('informes.*')" wire:navigate>
                        {{ __('Informes') }}
                    </flux:navlist.item>
                </flux:navlist.group>

            @endif
        </flux:navlist>

        <flux:spacer />

        {{-- Desktop User Menu --}}
        <flux:dropdown class="hidden lg:block" position="bottom" align="start">
            <flux:profile :name="auth()->user()->name" :initials="auth()->user()->initials()"
                icon:trailing="chevrons-up-down" />
            <flux:menu class="w-[220px]">
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                    {{ auth()->user()->initials() }}
                                </span>
                            </span>
                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>
                        {{ __('Ajustes') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                        {{ __('Cerrar sesión') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:sidebar>

    {{-- Mobile User Menu --}}
    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />
        <flux:spacer />
        <flux:dropdown position="top" align="end">
            <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />
            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-blue-200 dark:text-white">
                                    {{ auth()->user()->initials() }}
                                </span>
                            </span>
                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>
                        {{ __('Ajustes') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" icon="arrow-right-start-on-rectangle"
                    class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                        {{ __('Cerrar sesión') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    {{ $slot }}
</body>

</html>
