<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class RolePrivilegeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Si es admin, permitir todo
        Gate::before(function ($user) {
            return method_exists($user, 'isAdmin') && $user->isAdmin() ? true : null;
        });

        // Directiva Blade para roles
        Blade::if('role', function (...$roles) {
            $user = Auth::user();
            return $user && method_exists($user, 'hasRole') && $user->hasRole($roles);
        });

        // Directiva Blade para privilegios
        Blade::if('privilegio', function ($nombre) {
            $user = Auth::user();
            return $user && method_exists($user, 'hasPrivilege') && $user->hasPrivilege($nombre);
        });
    }
}
