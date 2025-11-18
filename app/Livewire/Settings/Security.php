<?php

namespace App\Livewire\Settings;

use App\Services\TwoFactorService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Security extends Component
{
    public bool $twoFactorEnabled = false;
    public ?string $qrCode = null;
    public ?string $secret = null;
    public array $recoveryCodes = [];
    public string $code = '';
    public array $passkeys = [];

    public function mount(TwoFactorService $twoFactor): void
    {
        $user = Auth::user();
        $this->twoFactorEnabled = $user->hasTwoFactorEnabled();

        if ($user->two_factor_secret) {
            $this->secret = decrypt($user->two_factor_secret);
            $this->qrCode = $twoFactor->qrcodeUrl($user, $this->secret);
            $this->recoveryCodes = $twoFactor->recoveryCodes($user);
        }

        $this->refreshPasskeys();
    }

    public function startTwoFactor(TwoFactorService $twoFactor): void
    {
        $user = Auth::user();
        $secret = $twoFactor->generateSecret();
        $codes = $twoFactor->generateRecoveryCodes();

        $twoFactor->storeForUser($user, $secret, $codes);

        $this->secret = $secret;
        $this->qrCode = $twoFactor->qrcodeUrl($user, $secret);
        $this->recoveryCodes = $codes;
        $this->twoFactorEnabled = false;
    }

    public function confirmTwoFactor(TwoFactorService $twoFactor): void
    {
        $user = Auth::user();

        $this->validate([
            'code' => ['required', 'string', 'digits:6'],
        ]);

        if (! $user->two_factor_secret) {
            $this->addError('code', __('Primero debes generar tu clave de 2FA.'));

            return;
        }

        $secret = decrypt($user->two_factor_secret);

        if (! $twoFactor->verify($secret, $this->code)) {
            $this->addError('code', __('El código TOTP no es válido.'));

            return;
        }

        $user->markTwoFactorAsVerified();
        Session::put('two_factor_passed', true);

        $this->twoFactorEnabled = true;
        $this->code = '';
    }

    public function disableTwoFactor(TwoFactorService $twoFactor): void
    {
        $twoFactor->disableForUser(Auth::user());
        $this->reset(['secret', 'qrCode', 'recoveryCodes', 'code']);
        $this->twoFactorEnabled = false;
    }

    public function regenerateRecoveryCodes(TwoFactorService $twoFactor): void
    {
        if (! Auth::user()->two_factor_secret) {
            return;
        }

        $codes = $twoFactor->generateRecoveryCodes();
        $twoFactor->storeForUser(Auth::user(), decrypt(Auth::user()->two_factor_secret), $codes);
        $this->recoveryCodes = $codes;
    }

    public function refreshPasskeys(): void
    {
        $this->passkeys = Auth::user()->passkeys()->latest()->get()->toArray();
    }
}
