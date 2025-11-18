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

        <div class="flex items-center justify-end">
            <flux:button variant="primary" type="submit" class="w-full">{{ __('Iniciar sesión') }}</flux:button>
        </div>
    </form>

    <div class="flex flex-col gap-2">
        <flux:button type="button" variant="secondary" class="w-full" onclick="loginWithPasskey()">
            {{ __('Entrar con Passkey') }}
        </flux:button>
        <p class="text-xs text-gray-500 text-center">{{ __('Usa una clave de acceso previamente registrada.') }}</p>
    </div>

    <script>
        function bufferDecode(value) {
            return Uint8Array.from(atob(value), c => c.charCodeAt(0));
        }

        function bufferEncode(value) {
            return btoa(String.fromCharCode(...new Uint8Array(value)));
        }

        async function loginWithPasskey() {
            if (!window.PublicKeyCredential) {
                alert('Tu navegador no soporta passkeys');
                return;
            }

            const optionsRequest = await fetch('{{ route('passkeys.login.options') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ email: document.querySelector('input[name="email"]').value })
            });

            const options = await optionsRequest.json();
            const publicKey = options.publicKey ?? options;

            publicKey.challenge = bufferDecode(publicKey.challenge);
            publicKey.allowCredentials = (publicKey.allowCredentials || []).map((cred) => ({
                ...cred,
                id: bufferDecode(cred.id)
            }));

            const assertion = await navigator.credentials.get({ publicKey });

            const data = {
                id: assertion.id,
                rawId: bufferEncode(assertion.rawId),
                type: assertion.type,
                response: {
                    clientDataJSON: bufferEncode(assertion.response.clientDataJSON),
                    authenticatorData: bufferEncode(assertion.response.authenticatorData),
                    signature: bufferEncode(assertion.response.signature),
                    userHandle: assertion.response.userHandle ? bufferEncode(assertion.response.userHandle) : null,
                },
            };

            const login = await fetch('{{ route('passkeys.login') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            if (login.ok) {
                const payload = await login.json();
                window.location.href = payload.redirect;
            } else {
                alert('No se pudo iniciar sesión con passkey');
            }
        }
    </script>
</div>
