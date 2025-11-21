<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\TwoFactorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TwoFactorController extends Controller
{
    public function enable(Request $request, TwoFactorService $twoFactorService): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string'],
        ]);

        $user = $request->user();
        $secret = $user->two_factor_secret
            ?? $request->input('secret')
            ?? $request->session()->get('two_factor_pending_secret');

        if (! $secret || ! $twoFactorService->verify($secret, $request->input('code'))) {
            return back()->withErrors(['code' => __('El código TOTP no es válido')]);
        }

        $recoveryCodes = $twoFactorService->generateRecoveryCodes();

        $user->forceFill([
            'two_factor_secret' => $secret,
            'two_factor_recovery_codes' => json_encode($recoveryCodes),
            'two_factor_confirmed_at' => now(),
            'two_factor_enabled' => true,
        ])->save();

        $request->session()->forget('two_factor_pending_secret');

        return back()->with('status', __('Autenticación de 2 pasos activada. Guarda tus códigos de recuperación.'));
    }

    public function regenerateRecoveryCodes(Request $request, TwoFactorService $twoFactorService): RedirectResponse
    {
        $user = $request->user();
        $user->forceFill([
            'two_factor_recovery_codes' => json_encode($twoFactorService->generateRecoveryCodes()),
        ])->save();

        return back()->with('status', __('Nuevos códigos de recuperación generados.'));
    }

    public function disable(Request $request): RedirectResponse
    {
        $user = $request->user();
        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
            'two_factor_enabled' => false,
        ])->save();

        $request->session()->forget(['two_factor_pending_secret', 'two_factor_passed']);

        return back()->with('status', __('Autenticación de 2 pasos desactivada.'));
    }
}
