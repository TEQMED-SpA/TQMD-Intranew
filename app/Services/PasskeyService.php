<?php

namespace App\Services;

use App\Models\Passkey;
use App\Models\User;
use Illuminate\Http\Request;
use Laragear\WebAuthn\Assertions\AssertionValidation;
use Laragear\WebAuthn\Attestations\AttestationCreation;
use Laragear\WebAuthn\Facades\WebAuthn;

class PasskeyService
{
    public function createRegistrationOptions(User $user): AttestationCreation
    {
        return WebAuthn::prepareAttestation($user);
    }

    public function storeCredential(Request $request, User $user): Passkey
    {
        $attestation = WebAuthn::validateAttestation($request, $user);

        return $user->passkeys()->create([
            'name' => $request->input('name', 'Passkey'),
            'credential_id' => base64_encode($attestation->credentialId),
            'public_key' => base64_encode($attestation->publicKey),
            'counter' => $attestation->counter,
            'transports' => $attestation->transports,
            'attestation_type' => $attestation->type,
            'backed_up' => $attestation->backupStatus,
            'device_type' => $attestation->deviceType,
        ]);
    }

    public function createLoginOptions(?string $email = null): AssertionValidation
    {
        return WebAuthn::prepareAssertion($email);
    }

    public function confirmLogin(Request $request): ?User
    {
        $assertion = WebAuthn::validateAssertion($request);

        $passkey = Passkey::where('credential_id', base64_encode($assertion->credentialId))->first();

        if (! $passkey) {
            return null;
        }

        $passkey->update([
            'counter' => $assertion->counter,
            'last_used_at' => now(),
        ]);

        return $passkey->user;
    }
}
