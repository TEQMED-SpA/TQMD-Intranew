<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureTwoFactorConfirmed
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Si no hay usuario autenticado, que siga normal
        if (! $user) {
            return $next($request);
        }

        // Si el usuario NO tiene 2FA activo, no hacemos nada
        if (! method_exists($user, 'hasTwoFactorEnabled') || ! $user->hasTwoFactorEnabled()) {
            return $next($request);
        }

        // Evitar bucles: dejamos pasar las rutas propias del 2FA y el logout
        if ($request->routeIs('two-factor.*') || $request->routeIs('logout')) {
            return $next($request);
        }

        // Leer el flag desde sesión (sin boolean())
        $twoFactorPassed = (bool) $request->session()->get('two_factor_passed', false);

        if (! $twoFactorPassed) {
            return redirect()->route('two-factor.challenge');
        }

        return $next($request);
    }
}
