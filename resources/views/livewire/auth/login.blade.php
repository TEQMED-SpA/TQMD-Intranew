<div class="flex flex-col gap-6">
    <x-auth-header :title="__('Intranet TEQMED')" :description="__('Ingrese su correo electrónico y contraseña a continuación para iniciar sesión')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="login" class="flex flex-col gap-6">
        <!-- Email Address -->
        <flux:input wire:model="email" :label="__('Correo electrónico')" type="email" required autofocus
            autocomplete="email" placeholder="johndoe@example.com" />

        <!-- Password -->
        <div class="relative">
            <flux:input wire:model="password" :label="__('Contraseña')" type="password" required
                autocomplete="current-password" :placeholder="__('Contraseña')" viewable />

            @if (Route::has('password.request'))
                <flux:link class="absolute end-0 top-0 text-sm" :href="route('password.request')" wire:navigate>
                    {{ __('¿Olvidaste tu contraseña?') }}
                </flux:link>
            @endif
        </div>

        <!-- Remember Me -->
        <flux:checkbox wire:model="remember" :label="__('Recordarme')" />
        <div class="flex flex-col gap-6" x-data="passkeyLogin()">
            <div class="flex items-center justify-end">
                <flux:button variant="primary" type="submit" class="w-full">
                    {{ __('Iniciar sesión') }}
                </flux:button>
            </div>
            <div class="mt-2">
                <flux:button type="button" variant="ghost" class="w-full" @click="loginWithPasskey">
                    {{ __('Iniciar sesión con passkey') }}
                </flux:button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
    <script>
        function getCsrfToken() {
            const meta = document.querySelector('meta[name="csrf-token"]');
            return meta ? meta.getAttribute('content') : '';
        }

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

        function passkeyLogin() {
            return {
                async loginWithPasskey() {
                    const csrf = getCsrfToken();

                    if (!window.PublicKeyCredential) {
                        alert('Tu navegador no soporta WebAuthn.');
                        return;
                    }

                    const emailInput = document.querySelector('input[type="email"]');
                    const email = emailInput ? emailInput.value : '';

                    // 1) Pedimos opciones al backend
                    const optionsResponse = await fetch('{{ route('passkeys.login.options.cust') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            email
                        }),
                    });

                    if (!optionsResponse.ok) {
                        const text = await optionsResponse.text();
                        console.error('Error al obtener opciones de login passkey', optionsResponse.status, text);
                        alert('Error al preparar el login con passkey.');
                        return;
                    }

                    const optionsJson = await optionsResponse.json();
                    const publicKey = optionsJson.publicKey ?? optionsJson;

                    // 2) Adaptar campos base64 → ArrayBuffer
                    publicKey.challenge = window.base64ToArrayBuffer(publicKey.challenge);

                    if (publicKey.allowCredentials) {
                        publicKey.allowCredentials = publicKey.allowCredentials.map((cred) => ({
                            ...cred,
                            id: window.base64ToArrayBuffer(cred.id),
                        }));
                    }

                    // 3) navigator.credentials.get()
                    const assertion = await navigator.credentials.get({
                        publicKey
                    });

                    // 4) Preparar payload estándar que espera AssertedRequest
                    const payload = {
                        id: assertion.id,
                        rawId: window.arrayBufferToBase64(assertion.rawId),
                        type: assertion.type,
                        response: {
                            clientDataJSON: window.arrayBufferToBase64(assertion.response.clientDataJSON),
                            authenticatorData: window.arrayBufferToBase64(assertion.response.authenticatorData),
                            signature: window.arrayBufferToBase64(assertion.response.signature),
                            userHandle: assertion.response.userHandle ?
                                window.arrayBufferToBase64(assertion.response.userHandle) : null,
                        },
                        clientExtensionResults: assertion.getClientExtensionResults ?
                            assertion.getClientExtensionResults() : {},
                    };

                    // 5) Enviar al backend para validar y loguear
                    const loginResponse = await fetch('{{ route('passkeys.login.cust') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(payload),
                    });

                    if (!loginResponse.ok) {
                        const text = await loginResponse.text();
                        console.error('Error al validar passkey', loginResponse.status, text);
                        alert('No se pudo validar la passkey.');
                        return;
                    }

                    const data = await loginResponse.json();

                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        window.location.reload();
                    }
                }
            }
        }
    </script>
@endpush
