<?php

namespace App\Services;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use PragmaRX\Google2FALaravel\Facades\Google2FA;

class TwoFactorService
{
    public function generateSecret(): string
    {
        return Google2FA::generateSecretKey();
    }

    public function qrcodeUrl(Authenticatable $user, string $secret): string
    {
        return Google2FA::getQRCodeInline(
            config('app.name'),
            $user->email,
            $secret
        );
    }

    public function verify(string $secret, string $code): bool
    {
        return Google2FA::verifyKey($secret, $code);
    }

    public function generateRecoveryCodes(): array
    {
        return collect(range(1, 8))
            ->map(fn () => Str::upper(Str::random(10)))
            ->values()
            ->all();
    }

    public function storeForUser(Authenticatable $user, string $secret, array $recoveryCodes): void
    {
        $user->forceFill([
            'two_factor_secret' => Crypt::encryptString($secret),
            'two_factor_recovery_codes' => Crypt::encryptString(json_encode($recoveryCodes)),
            'two_factor_confirmed_at' => null,
        ])->save();
    }

    public function disableForUser(Authenticatable $user): void
    {
        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();
    }

    public function recoveryCodes(Authenticatable $user): array
    {
        $codes = $user->two_factor_recovery_codes;

        if (! $codes) {
            return [];
        }

        return json_decode(Crypt::decryptString($codes), true) ?: [];
    }

    public function confirmUsingRecoveryCode(Authenticatable $user, string $code): bool
    {
        $codes = $this->recoveryCodes($user);

        if (! in_array($code, $codes)) {
            return false;
        }

        $remaining = Arr::where($codes, fn ($item) => $item !== $code);
        $user->forceFill([
            'two_factor_recovery_codes' => Crypt::encryptString(json_encode(array_values($remaining))),
        ])->save();

        return true;
    }
}
