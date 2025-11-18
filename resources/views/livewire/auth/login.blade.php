<div class="flex flex-col gap-6" x-data="passkeyLogin(@entangle('email'))">
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
        <div class="flex items-center justify-between gap-2">
            <div class="h-px flex-1 bg-slate-200 dark:bg-slate-700"></div>
            <span class="text-xs uppercase tracking-wide text-slate-500">{{ __('o') }}</span>
            <div class="h-px flex-1 bg-slate-200 dark:bg-slate-700"></div>
        </div>
        <flux:button type="button" variant="ghost" class="w-full" @click="loginWithPasskey">{{ __('Entrar con passkey') }}</flux:button>
    </div>

</div>

@push('scripts')
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const b64ToBuffer = (input) => Uint8Array.from(atob(input.replace(/-/g, '+').replace(/_/g, '/')), c => c.charCodeAt(0)).buffer;
        const bufferToB64 = (buffer) => {
            const bytes = new Uint8Array(buffer);
            let binary = '';
            bytes.forEach(b => binary += String.fromCharCode(b));
            return btoa(binary).replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');
        };

        function passkeyLogin(initialEmail) {
            return {
                email: initialEmail,
                async loginWithPasskey() {
                    if (!window.PublicKeyCredential) {
                        alert('Tu navegador no soporta passkeys');
                        return;
                    }

                    const optionsResponse = await fetch('{{ route('passkeys.login.options') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                        body: JSON.stringify({ email: this.email })
                    });

                    const optionsJson = await optionsResponse.json();
                    const publicKey = optionsJson.publicKey ?? optionsJson;

                    publicKey.challenge = b64ToBuffer(publicKey.challenge);

                    if (publicKey.allowCredentials) {
                        publicKey.allowCredentials = publicKey.allowCredentials.map(cred => ({
                            ...cred,
                            id: b64ToBuffer(cred.id),
                        }));
                    }

                    const assertion = await navigator.credentials.get({ publicKey });

                    const data = {
                        id: assertion.id,
                        rawId: bufferToB64(assertion.rawId),
                        type: assertion.type,
                        response: {
                            authenticatorData: bufferToB64(assertion.response.authenticatorData),
                            clientDataJSON: bufferToB64(assertion.response.clientDataJSON),
                            signature: bufferToB64(assertion.response.signature),
                            userHandle: assertion.response.userHandle ? bufferToB64(assertion.response.userHandle) : null,
                        },
                    };

                    const loginResponse = await fetch('{{ route('passkeys.login') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                        body: JSON.stringify(data)
                    });

                    const result = await loginResponse.json();

                    if (result.redirect) {
                        window.location.href = result.redirect;
                    }
                }
            }
        }
    </script>
@endpush
