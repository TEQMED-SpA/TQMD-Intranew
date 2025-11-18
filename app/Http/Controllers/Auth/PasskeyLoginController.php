<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\PasskeyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class PasskeyLoginController extends Controller
{
    public function options(Request $request, PasskeyService $passkeyService): JsonResponse
    {
        $request->validate([
            'email' => ['nullable', 'email'],
        ]);

        return response()->json($passkeyService->createLoginOptions($request->input('email')));
    }

    public function login(Request $request, PasskeyService $passkeyService): JsonResponse
    {
        $user = $passkeyService->confirmLogin($request);

        if (! $user) {
            throw ValidationException::withMessages([
                'email' => __('No se pudo validar la passkey proporcionada.'),
            ]);
        }

        Auth::login($user, true);
        Session::put('two_factor_passed', true);
        Session::regenerate();

        return response()->json([
            'redirect' => route('dashboard', absolute: false),
        ]);
    }
}
