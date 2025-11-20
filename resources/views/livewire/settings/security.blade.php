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
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        {{ __('Escanea el código QR con Google Authenticator/Authenticator y confirma con un código para activar la protección.') }}
                    </p>

                    @if ($qrCode)
                        <div class="rounded-lg bg-gray-50 p-4 text-center dark:bg-gray-800">
                            <div class="flex justify-center">
                                {!! $qrCode !!}
                            </div>
                            <p class="mt-2 text-xs text-gray-500">{{ __('Si no puedes escanear el QR, usa la clave:') }}
                            </p>
                            <p class="font-mono text-sm">{{ $secret }}</p>
                        </div>
                    @endif

                    <div class="space-y-2">
                        <flux:input wire:model="code" :label="__('Código de verificación')" maxlength="6" />
                        <flux:button wire:click="confirmTwoFactor" variant="primary">{{ __('Confirmar y activar 2FA') }}
                        </flux:button>
                        <flux:button wire:click="startTwoFactor" variant="ghost">{{ __('Generar nuevo QR') }}
                        </flux:button>
                    </div>
                @else
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        {{ __('Cada inicio de sesión pedirá un código TOTP o un código de recuperación.') }}</p>

                    <div class="space-y-2">
                        <flux:button wire:click="regenerateRecoveryCodes" variant="ghost">
                            {{ __('Regenerar códigos de recuperación') }}</flux:button>
                        <flux:button wire:click="disableTwoFactor" variant="danger">{{ __('Desactivar 2FA') }}
                        </flux:button>
                    </div>
                @endif

                @if (count($recoveryCodes) > 0)
                    <div class="mt-4 rounded-lg bg-slate-50 p-4 text-sm shadow dark:bg-slate-800">
                        <p class="font-semibold text-slate-900 dark:text-slate-100">
                            {{ __('Códigos de recuperación') }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ __('Guárdalos en un lugar seguro. Cada código se consume al usarlo.') }}
                        </p>

                        <div class="mt-2 grid grid-cols-2 gap-2 font-mono text-xs">
                            @foreach ($recoveryCodes as $code)
                                <span
                                    class="rounded bg-white px-2 py-1 shadow-sm text-slate-900 dark:bg-slate-900 dark:text-slate-100">
                                    {{ $code }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>

            <div class="space-y-4">
                <h3 class="text-lg font-semibold">{{ __('Passkeys / WebAuthn') }}</h3>
                <p class="text-sm text-gray-600 dark:text-gray-300">
                    {{ __('Usa passkeys para iniciar sesión sin contraseña. Registra dispositivos desde aquí y bórralos cuando sea necesario.') }}
                </p>

                <div class="space-y-2">
                    <flux:input x-model="passkeyName" :label="__('Nombre del dispositivo')"
                        placeholder="{{ __('MacBook, iPhone, etc.') }}" />
                    <flux:button type="button" @click="registerPasskey" variant="primary">
                        {{ __('Registrar passkey') }}</flux:button>
                </div>

                <div class="space-y-2">
                    <p class="text-sm font-semibold">{{ __('Dispositivos registrados') }}</p>
                    <template x-if="passkeys.length === 0">
                        <p class="text-xs text-gray-500">{{ __('Aún no tienes passkeys guardadas.') }}</p>
                    </template>
                    <div class="space-y-2">
                        <template x-for="item in passkeys" :key="item.id">
                            <div
                                class="flex items-center justify-between rounded border border-slate-200 p-3 dark:border-slate-700">
                                <div>
                                    <p class="font-semibold" x-text="item.name || '{{ __('Sin nombre') }}'"></p>
                                    <p class="text-xs text-gray-500" x-text="item.created_at"></p>
                                </div>
                                <flux:button type="button" variant="ghost" class="!text-red-500"
                                    @click="deletePasskey(item.id)">{{ __('Eliminar') }}</flux:button>
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
        // Esta función se puede redeclarar sin problema (function), no da SyntaxError.
        function getCsrfToken() {
            const meta = document.querySelector('meta[name="csrf-token"]');
            return meta ? meta.getAttribute('content') : '';
        }

        // Helpers globales para WebAuthn: se definen una sola vez en window
        if (!window.base64ToArrayBuffer) {
            window.base64ToArrayBuffer = (base64) => {
                const binary = atob(base64.replace(/-/g, '+').replace(/_/g, '/'));
                const bytes = new Uint8Array(binary.length);
                for (let i = 0; i < binary.length; i++) {
                    bytes[i] = binary.charCodeAt(i);
                }
                return bytes.buffer;
            };
        }

        if (!window.arrayBufferToBase64) {
            window.arrayBufferToBase64 = (buffer) => {
                const bytes = new Uint8Array(buffer);
                let binary = '';
                for (let i = 0; i < bytes.byteLength; i++) {
                    binary += String.fromCharCode(bytes[i]);
                }
                return btoa(binary)
                    .replace(/\+/g, '-')
                    .replace(/\//g, '_')
                    .replace(/=+$/, '');
            };
        }

        // Alpine component para gestionar Passkeys
        function passkeyManager() {
            return {
                passkeys: @js($passkeys),
                passkeyName: '',

                async registerPasskey() {
                    const csrf = getCsrfToken();

                    if (!window.PublicKeyCredential) {
                        alert('Tu navegador no soporta WebAuthn');
                        return;
                    }

                    if (!csrf) {
                        console.error('CSRF token no encontrado');
                        alert('Error al obtener el token CSRF. Recarga la página.');
                        return;
                    }

                    // 1) Pedimos las opciones al backend
                    const optionsResponse = await fetch('{{ route('passkeys.options') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json',
                        },
                    });

                    if (!optionsResponse.ok) {
                        const text = await optionsResponse.text();
                        console.error('Error al obtener opciones de passkey', optionsResponse.status, text);
                        alert('Error al obtener las opciones de passkey. Revisa la consola o el log de Laravel.');
                        return;
                    }

                    const optionsJson = await optionsResponse.json();
                    const publicKey = optionsJson.publicKey ?? optionsJson;

                    // 2) Adaptar campos base64 → ArrayBuffer
                    publicKey.challenge = window.base64ToArrayBuffer(publicKey.challenge);
                    publicKey.user.id = window.base64ToArrayBuffer(publicKey.user.id);

                    if (publicKey.excludeCredentials) {
                        publicKey.excludeCredentials = publicKey.excludeCredentials.map((cred) => ({
                            ...cred,
                            id: window.base64ToArrayBuffer(cred.id),
                        }));
                    }

                    // 3) Llamar a navigator.credentials.create()
                    const credential = await navigator.credentials.create({
                        publicKey
                    });

                    // 4) Preparar el payload en el formato estándar que AttestedRequest espera
                    const attestation = {
                        id: credential.id,
                        rawId: window.arrayBufferToBase64(credential.rawId),
                        type: credential.type,
                        response: {
                            clientDataJSON: window.arrayBufferToBase64(credential.response.clientDataJSON),
                            attestationObject: window.arrayBufferToBase64(credential.response.attestationObject),
                        },
                        clientExtensionResults: credential.getClientExtensionResults ?
                            credential.getClientExtensionResults() : {},
                    };

                    // 5) Enviar al backend (sin envolver en "attestation", todo plano)
                    const storeResponse = await fetch('{{ route('passkeys.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            name: this.passkeyName || 'Passkey',
                            ...attestation,
                        }),
                    });

                    if (!storeResponse.ok) {
                        const text = await storeResponse.text();
                        console.error('Error al registrar passkey', storeResponse.status, text);
                        alert('Error al registrar la passkey. Revisa el log del servidor.');
                        return;
                    }

                    this.passkeyName = '';
                    await this.loadPasskeys();
                }


                async loadPasskeys() {
                    const csrf = getCsrfToken();
                    const list = await fetch('{{ route('passkeys.index') }}', {
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                        },
                    });

                    this.passkeys = await list.json();
                },

                async deletePasskey(id) {
                    const csrf = getCsrfToken();
                    await fetch('{{ url('passkeys') }}/' + id, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                        },
                    });

                    await this.loadPasskeys();
                },
            };
        }
    </script>
@endpush
