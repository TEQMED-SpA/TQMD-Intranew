<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PrivilegeMiddleware
{
    public function handle(Request $request, Closure $next, ...$privileges): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(403, 'No autenticado.');
        }

        if ($user->isAdmin()) {
            return $next($request);
        }

        if ($user->hasAnyPrivilege($privileges)) {
            return $next($request);
        }

        abort(403, 'No tienes los privilegios requeridos.');
    }
}
