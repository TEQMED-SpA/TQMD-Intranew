<?php

namespace App\Services;

use App\Models\Passkey;
use App\Models\User;
use Illuminate\Http\Request;
use Laragear\WebAuthn\Assertions\CredentialAssertionRequestOptions;
use Laragear\WebAuthn\Assertions\CredentialAssertionValidator;
use Laragear\WebAuthn\Attestations\CredentialCreationRequestOptions;
use Laragear\WebAuthn\Attestations\CredentialAttestationValidator;
use Laragear\WebAuthn\Enums\ChallengeType;
use Laragear\WebAuthn\WebAuthn;


class WebAuthnService
{
    public function __construct(
        protected WebAuthn $webAuthn,
    ) {}

    /**
     * Opciones para registrar un passkey (navigator.credentials.create).
     */
    public function creationOptions(User $user): CredentialCreationRequestOptions
    {
        return $this->webAuthn
            ->generateAttestation($user)
            ->setUserHandle((string) $user->getAuthIdentifier())
            ->setTimeout(60000)
            ->setChallengeType(ChallengeType::Create);
    }

    /**
     * Valida la attestation y crea tanto la credencial WebAuthn
     * como el registro en tu tabla `passkeys`.
     */
    public function storePasskey(Request $request, User $user, string $name): Passkey
    {
        $attestation = $this->webAuthn->validateAttestation($request);

        // Guardamos SOLO en tu tabla passkeys
        return Passkey::create([
            'user_id'         => $user->id,
            'name'            => $name,
            'credential_id'   => base64_encode($attestation->credentialId),
            'public_key'      => base64_encode($attestation->publicKey),
            'counter'         => $attestation->counter,
            'transports'      => is_array($attestation->transports)
                ? implode(',', $attestation->transports)
                : (string) $attestation->transports,
            'attestation_type' => $attestation->type,
            'last_used_at'    => now(),
        ]);
    }


    /**
     * Opciones para login con passkey (navigator.credentials.get).
     */
    public function assertionOptions(?User $user = null): CredentialAssertionRequestOptions
    {
        return $this->webAuthn
            ->generateAssertion($user)
            ->setTimeout(60000)
            ->setUserVerification(); // puedes pasar 'required' / 'preferred' / 'discouraged' según tu config
    }

    /**
     * Valida el assertion del navegador y devuelve el usuario autenticado.
     */
    public function validateAssertion(Request $request): ?User
    {
        $assertion = $this->webAuthn->validateAssertion($request);

        $passkey = Passkey::where(
            'credential_id',
            base64_encode($assertion->credentialId)
        )->first();

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
