<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\TwoFactorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class TwoFactorChallengeController extends Controller
{
    public function show()
    {
        return view('auth.two-factor-challenge');
    }

    public function store(Request $request, TwoFactorService $twoFactor)
    {
        $request->validate([
            'code' => ['required', 'string'],
        ]);

        $user = $request->user();

        if (! $user || ! $user->two_factor_secret) {
            abort(403);
        }

        try {
            $secret = Crypt::decryptString($user->two_factor_secret);
        } catch (\Throwable $e) {
            throw ValidationException::withMessages([
                'code' => __('No se pudo validar tu código, vuelve a configurar el 2FA.'),
            ]);
        }

        $code = str_replace(' ', '', $request->string('code'));

        if ($twoFactor->verify($secret, $code) || $twoFactor->confirmUsingRecoveryCode($user, $code)) {
            $user->markTwoFactorAsVerified();
            Session::put('two_factor_passed', true);

            return redirect()->intended(route('dashboard'));
        }

        throw ValidationException::withMessages([
            'code' => __('El código no es válido.'),
        ]);
    }
}
