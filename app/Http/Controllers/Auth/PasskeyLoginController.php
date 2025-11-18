<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\WebAuthnService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class PasskeyLoginController extends Controller
{
    public function options(Request $request, WebAuthnService $service)
    {
        $user = null;

        if ($request->filled('email')) {
            $user = User::where('email', $request->string('email'))->first();
        }

        return response()->json($service->assertionOptions($user));
    }

    public function login(Request $request, WebAuthnService $service)
    {
        $user = $service->validateAssertion($request);

        if (! $user) {
            throw ValidationException::withMessages([
                'passkey' => __('No se pudo validar tu passkey.'),
            ]);
        }

        Auth::login($user, true);
        Session::put('two_factor_passed', true);
        Session::regenerate();

        return response()->json([
            'redirect' => route('dashboard'),
        ]);
    }
}
