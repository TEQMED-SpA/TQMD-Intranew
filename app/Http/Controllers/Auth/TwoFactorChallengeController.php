<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\TwoFactorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class TwoFactorChallengeController extends Controller
{
    public function create()
    {
        return view('auth.two-factor-challenge');
    }

    public function store(Request $request, TwoFactorService $twoFactorService): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string'],
        ]);

        $user = $request->user();

        if (! $user || ! $twoFactorService->verify((string) $user->two_factor_secret, $request->input('code'))) {
            return back()->withErrors(['code' => __('Código no válido')]);
        }

        Session::put('two_factor_passed', true);
        Session::regenerate();

        return redirect()->intended(route('dashboard'));
    }
}
