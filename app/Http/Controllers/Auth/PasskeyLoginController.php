<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Laragear\WebAuthn\Http\Requests\AssertionRequest;
use Laragear\WebAuthn\Http\Requests\AssertedRequest;

class PasskeyLoginController extends Controller
{
    /**
     * Devuelve las opciones para navigator.credentials.get()
     * (login con passkey).
     *
     * Se llama vía POST desde el login con JSON { email: "..."} opcional.
     */
    public function options(AssertionRequest $request)
    {
        $user = null;

        if ($request->filled('email')) {
            $user = User::where('email', $request->string('email'))->first();
        }

        // toVerify() genera las opciones de assertion basadas en el usuario (o userless)
        return response()->json(
            $request->toVerify($user)
        );
    }

    /**
     * Recibe el assertion de navigator.credentials.get() y autentica al usuario.
     */
    public function login(AssertedRequest $request)
    {
        // AssertedRequest ya validó la firma, challenge, etc.
        // El usuario autenticado viene en $request->user()
        $user = $request->user();

        if (! $user instanceof User) {
            throw ValidationException::withMessages([
                'passkey' => __('No se pudo validar tu passkey.'),
            ]);
        }

        // Login de Laravel
        Auth::login($user, true);
        Session::regenerate();

        // Si usas 2FA TOTP, checkeamos si está habilitado
        if (method_exists($user, 'hasTwoFactorEnabled') && $user->hasTwoFactorEnabled()) {
            Session::put('two_factor_passed', false);

            return response()->json([
                'redirect' => route('two-factor.challenge'),
            ]);
        }

        Session::put('two_factor_passed', true);

        return response()->json([
            'redirect' => route('dashboard'),
        ]);
    }
}
