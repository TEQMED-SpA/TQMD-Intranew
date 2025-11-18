<?php

namespace App\Http\Controllers;

use App\Services\TwoFactorService;
use Illuminate\Http\Request;

class SecurityController extends Controller
{
    public function __invoke(Request $request, TwoFactorService $twoFactorService)
    {
        $user = $request->user();

        $pendingSecret = $user->two_factor_secret ?? $request->session()->get('two_factor_pending_secret');

        if (! $pendingSecret) {
            $pendingSecret = $twoFactorService->generateSecret();
            $request->session()->put('two_factor_pending_secret', $pendingSecret);
        }

        $qrCodeUrl = $twoFactorService->getQrCodeUrl(config('app.name'), $user->email, $pendingSecret);
        $recoveryCodes = $user->two_factor_recovery_codes ? json_decode($user->two_factor_recovery_codes, true) : [];

        return view('settings.security', [
            'user' => $user,
            'pendingSecret' => $pendingSecret,
            'qrCodeUrl' => $qrCodeUrl,
            'recoveryCodes' => $recoveryCodes,
        ]);
    }
}
