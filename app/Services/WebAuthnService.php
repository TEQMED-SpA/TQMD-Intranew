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
    public function __construct(protected WebAuthn $webAuthn)
    {
    }

    public function creationOptions(User $user): CredentialCreationRequestOptions
    {
        return $this->webAuthn
            ->generateAttestation($user)
            ->setUserHandle((string) $user->getAuthIdentifier())
            ->setTimeout(60000)
            ->setChallengeType(ChallengeType::Create);
    }

    public function storePasskey(Request $request, User $user, string $name): Passkey
    {
        /** @var CredentialAttestationValidator $attestation */
        $attestation = $this->webAuthn->validateAttestation($request);

        $credential = $attestation->save(
            user: $user,
            name: $name,
        );

        return Passkey::create([
            'user_id' => $user->id,
            'name' => $name,
            'credential_id' => base64_encode($credential->id),
            'public_key' => base64_encode($credential->public_key),
            'counter' => $credential->counter,
            'transports' => implode(',', $credential->transports ?? []),
            'attestation_type' => $credential->attestation_type ?? null,
        ]);
    }

    public function assertionOptions(?User $user = null): CredentialAssertionRequestOptions
    {
        return $this->webAuthn
            ->generateAssertion($user)
            ->setTimeout(60000)
            ->setUserVerification();
    }

    public function validateAssertion(Request $request): ?User
    {
        /** @var CredentialAssertionValidator $assertion */
        $assertion = $this->webAuthn->validateAssertion($request);

        if ($assertion->user instanceof User) {
            $assertion->user->forceFill(['last_passkey_at' => now()])->save();
        }

        return $assertion->user;
    }
}
