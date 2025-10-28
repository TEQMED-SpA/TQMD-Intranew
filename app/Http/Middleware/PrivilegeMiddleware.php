<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class PrivilegeMiddleware
{
    public function handle(Request $request, Closure $next, ...$privileges): Response
    {
        $user = $request->user();
        if (!$user) {
            abort(401, 'No autenticado.');
        }

        // Normaliza “privilege:a,b,c”
        $privileges = collect($privileges)
            ->flatMap(fn($p) => is_string($p) ? array_map('trim', explode(',', $p)) : [$p])
            ->filter(fn($p) => is_string($p) && $p !== '')
            ->values()
            ->all();

        if (empty($privileges)) {
            abort(500, 'Error de configuración de privilegios.');
        }

        // Obtiene privilegios del usuario (null-safe)
        $userPrivileges = $user->rol?->privilegios?->pluck('nombre')->filter()->values()->all() ?? [];

        // Atajo para admins, si existe el método
        if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
            return $next($request);
        }

        // Usa método de dominio si existe
        if (method_exists($user, 'tienePrivilegio')) {
            if ($user->tienePrivilegio($privileges)) return $next($request);
        } else {
            if (!empty(array_intersect($userPrivileges, $privileges))) return $next($request);
        }

        Log::warning('[PrivilegeMW] Acceso denegado', [
            'route' => optional($request->route())->getName(),
            'user'  => $user->id ?? null,
            'need'  => $privileges,
            'has'   => $userPrivileges,
        ]);

        abort(403, 'No tienes los privilegios requeridos.');
    }
}
