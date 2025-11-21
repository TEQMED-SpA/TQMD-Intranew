<?php

namespace App\Livewire\Settings;

use App\Services\TwoFactorService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
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
            try {
                $this->secret = Crypt::decryptString($user->two_factor_secret);
            } catch (\Throwable $e) {
                $this->secret = null;
            }

            if ($this->secret) {
                $this->qrCode = $twoFactor->qrcodeUrl($user, $this->secret);
            }

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

        try {
            $secret = Crypt::decryptString($user->two_factor_secret);
        } catch (\Throwable $e) {
            $this->addError('code', __('No se pudo leer tu secreto 2FA, vuelve a generarlo.'));
            return;
        }

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
        $user = Auth::user();

        if (! $user->two_factor_secret) {
            return;
        }

        try {
            $secret = Crypt::decryptString($user->two_factor_secret);
        } catch (\Throwable $e) {
            return;
        }

        $codes = $twoFactor->generateRecoveryCodes();
        $twoFactor->storeForUser($user, $secret, $codes);
        $this->recoveryCodes = $codes;
    }

    public function refreshPasskeys(): void
    {
        $this->passkeys = Auth::user()->passkeys()
            ->latest()
            ->get()
            ->map(function ($pk) {
                return [
                    'id'         => $pk->id,
                    'name'       => $pk->name ?? 'Passkey',
                    'created_at' => optional($pk->created_at)->toDateTimeString(),
                ];
            })
            ->toArray();
    }

    public function render()
    {
        return view('livewire.settings.security');
    }
}
