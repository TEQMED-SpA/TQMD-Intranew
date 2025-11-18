<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTwoFactorConfirmed
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->hasTwoFactorEnabled()) {
            return $next($request);
        }

        if ($request->session()->boolean('two_factor_passed')) {
            return $next($request);
        }

        if ($request->routeIs('two-factor.challenge', 'two-factor.challenge.store')) {
            return $next($request);
        }

        return redirect()->route('two-factor.challenge');
    }
}
