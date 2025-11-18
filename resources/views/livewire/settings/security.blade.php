<section class="w-full" x-data="passkeyManager()">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Seguridad')" :subheading="__('Configura Passkeys y autenticación de dos pasos (TOTP)')">
        <div class="grid gap-8 md:grid-cols-2">
            <div class="space-y-4">
                <h3 class="text-lg font-semibold">{{ __('Autenticación en dos pasos (TOTP)') }}</h3>

                @if ($twoFactorEnabled)
                    <flux:badge color="green">{{ __('Activo') }}</flux:badge>
                @else
                    <flux:badge color="zinc">{{ __('Pendiente de activación') }}</flux:badge>
                @endif

                @if (!$twoFactorEnabled)
                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ __('Escanea el código QR con Google Authenticator/Authenticator y confirma con un código para activar la protección.') }}</p>

                    @if ($qrCode)
                        <div class="rounded-lg bg-gray-50 p-4 text-center dark:bg-gray-800">
                            <div class="flex justify-center">
                                {!! $qrCode !!}
                            </div>
                            <p class="mt-2 text-xs text-gray-500">{{ __('Si no puedes escanear el QR, usa la clave:') }}</p>
                            <p class="font-mono text-sm">{{ $secret }}</p>
                        </div>
                    @endif

                    <div class="space-y-2">
                        <flux:input wire:model="code" :label="__('Código de verificación')" maxlength="6" />
                        <flux:button wire:click="confirmTwoFactor" variant="primary">{{ __('Confirmar y activar 2FA') }}</flux:button>
                        <flux:button wire:click="startTwoFactor" variant="ghost">{{ __('Generar nuevo QR') }}</flux:button>
                    </div>
                @else
                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ __('Cada inicio de sesión pedirá un código TOTP o un código de recuperación.') }}</p>

                    <div class="space-y-2">
                        <flux:button wire:click="regenerateRecoveryCodes" variant="ghost">{{ __('Regenerar códigos de recuperación') }}</flux:button>
                        <flux:button wire:click="disableTwoFactor" variant="danger">{{ __('Desactivar 2FA') }}</flux:button>
                    </div>
                @endif

                @if (count($recoveryCodes) > 0)
                    <div class="mt-4 rounded-lg bg-slate-50 p-4 text-sm shadow dark:bg-slate-800">
                        <p class="font-semibold">{{ __('Códigos de recuperación') }}</p>
                        <p class="text-xs text-gray-500">{{ __('Guárdalos en un lugar seguro. Cada código se consume al usarlo.') }}</p>
                        <div class="mt-2 grid grid-cols-2 gap-2 font-mono text-xs">
                            @foreach ($recoveryCodes as $code)
                                <span class="rounded bg-white px-2 py-1 shadow-sm dark:bg-slate-900">{{ $code }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <div class="space-y-4">
                <h3 class="text-lg font-semibold">{{ __('Passkeys / WebAuthn') }}</h3>
                <p class="text-sm text-gray-600 dark:text-gray-300">{{ __('Usa passkeys para iniciar sesión sin contraseña. Registra dispositivos desde aquí y bórralos cuando sea necesario.') }}</p>

                <div class="space-y-2">
                    <flux:input x-model="passkeyName" :label="__('Nombre del dispositivo')" placeholder="{{ __('MacBook, iPhone, etc.') }}" />
                    <flux:button type="button" @click="registerPasskey" variant="primary">{{ __('Registrar passkey') }}</flux:button>
                </div>

                <div class="space-y-2">
                    <p class="text-sm font-semibold">{{ __('Dispositivos registrados') }}</p>
                    <template x-if="passkeys.length === 0">
                        <p class="text-xs text-gray-500">{{ __('Aún no tienes passkeys guardadas.') }}</p>
                    </template>
                    <div class="space-y-2">
                        <template x-for="item in passkeys" :key="item.id">
                            <div class="flex items-center justify-between rounded border border-slate-200 p-3 dark:border-slate-700">
                                <div>
                                    <p class="font-semibold" x-text="item.name || '{{ __('Sin nombre') }}'"></p>
                                    <p class="text-xs text-gray-500" x-text="item.created_at"></p>
                                </div>
                                <flux:button type="button" variant="ghost" class="!text-red-500" @click="deletePasskey(item.id)">{{ __('Eliminar') }}</flux:button>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </x-settings.layout>
</section>

@push('scripts')
    <script>
        const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const base64ToArrayBuffer = (base64) => {
            const binary = atob(base64.replace(/-/g, '+').replace(/_/g, '/'));
            const bytes = new Uint8Array(binary.length);
            for (let i = 0; i < binary.length; i++) bytes[i] = binary.charCodeAt(i);
            return bytes.buffer;
        };

        const arrayBufferToBase64 = (buffer) => {
            const bytes = new Uint8Array(buffer);
            let binary = '';
            for (let i = 0; i < bytes.byteLength; i++) binary += String.fromCharCode(bytes[i]);
            return btoa(binary).replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');
        };

        function passkeyManager() {
            return {
                passkeys: @js($passkeys),
                passkeyName: '',
                async registerPasskey() {
                    if (!window.PublicKeyCredential) {
                        alert('Tu navegador no soporta WebAuthn');
                        return;
                    }

                    const optionsResponse = await fetch('{{ route('passkeys.options') }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrf },
                    });

                    const optionsJson = await optionsResponse.json();
                    const publicKey = optionsJson.publicKey ?? optionsJson;

                    publicKey.challenge = base64ToArrayBuffer(publicKey.challenge);
                    publicKey.user.id = base64ToArrayBuffer(publicKey.user.id);

                    if (publicKey.excludeCredentials) {
                        publicKey.excludeCredentials = publicKey.excludeCredentials.map(cred => ({
                            ...cred,
                            id: base64ToArrayBuffer(cred.id),
                        }));
                    }

                    const credential = await navigator.credentials.create({ publicKey });

                    const attestation = {
                        id: credential.id,
                        rawId: arrayBufferToBase64(credential.rawId),
                        type: credential.type,
                        response: {
                            clientDataJSON: arrayBufferToBase64(credential.response.clientDataJSON),
                            attestationObject: arrayBufferToBase64(credential.response.attestationObject),
                        },
                    };

                    await fetch('{{ route('passkeys.store') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                        body: JSON.stringify({
                            name: this.passkeyName || 'Passkey',
                            attestation,
                        }),
                    });

                    this.passkeyName = '';
                    await this.loadPasskeys();
                },
                async loadPasskeys() {
                    const list = await fetch('{{ route('passkeys.index') }}', { headers: { 'X-CSRF-TOKEN': csrf } });
                    this.passkeys = await list.json();
                },
                async deletePasskey(id) {
                    await fetch('{{ url('passkeys') }}/' + id, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': csrf },
                    });

                    await this.loadPasskeys();
                },
            }
        }
    </script>
@endpush
