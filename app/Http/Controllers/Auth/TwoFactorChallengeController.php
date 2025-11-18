<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\TwoFactorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        $secret = decrypt($user->two_factor_secret);
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
