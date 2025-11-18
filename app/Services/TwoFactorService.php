<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Contracts\Google2FA as Google2FAContract;

class TwoFactorService
{
    public function __construct(private readonly Google2FAContract $google2fa)
    {
    }

    public function generateSecret(): string
    {
        return $this->google2fa->generateSecretKey();
    }

    public function getQrCodeUrl(string $company, string $email, string $secret): string
    {
        return $this->google2fa->getQRCodeUrl($company, $email, $secret);
    }

    public function verify(string $secret, string $code): bool
    {
        return $this->google2fa->verifyKey($secret, $code);
    }

    public function generateRecoveryCodes(int $count = 8): array
    {
        return collect(range(1, $count))
            ->map(fn () => Str::upper(Str::random(10)))
            ->toArray();
    }

    public function redactSecret(?string $secret): ?string
    {
        if (! $secret) {
            return null;
        }

        return Arr::join([substr($secret, 0, 4), '••••', substr($secret, -4)], ' ');
    }
}
