<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(403, 'No autenticado.');
        }

        if ($user->isAdmin()) {
            return $next($request); // admin pasa siempre
        }

        if ($user->hasRole($roles)) {
            return $next($request);
        }

        abort(403, 'No tienes el rol requerido.');
    }
}
