@php
    use SimpleSoftwareIO\QrCode\Facades\QrCode;
@endphp

<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Seguridad')" :subheading="__('Passkeys (WebAuthn) y autenticación en dos pasos')">
        @if (session('status'))
            <div class="rounded-md bg-green-50 p-4 text-sm text-green-700">
                {{ session('status') }}
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-2">
            <div class="space-y-4 rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <h3 class="text-lg font-semibold">{{ __('Autenticación TOTP') }}</h3>
                <p class="text-sm text-gray-600">{{ __('Protege tu cuenta con una app como Google Authenticator o Authy.') }}</p>

                @if ($user->hasEnabledTwoFactor())
                    <div class="flex items-center gap-2 text-green-700">
                        <span class="h-2 w-2 rounded-full bg-green-600"></span>
                        <p class="text-sm font-medium">{{ __('2FA activado') }}</p>
                    </div>

                    <form action="{{ route('two-factor.disable') }}" method="POST" class="mt-4">
                        @csrf
                        @method('DELETE')
                        <flux:button type="submit" variant="danger">{{ __('Desactivar 2FA') }}</flux:button>
                    </form>
                @else
                    <div class="flex items-center gap-2 text-red-700">
                        <span class="h-2 w-2 rounded-full bg-red-600"></span>
                        <p class="text-sm font-medium">{{ __('2FA desactivado') }}</p>
                    </div>
                @endif

                <div class="mt-4 space-y-3">
                    <p class="text-sm text-gray-700">{{ __('Escanea el código QR con tu app TOTP y luego ingresa el código de 6 dígitos para activar.') }}</p>

                    <div class="flex flex-col items-center justify-center rounded-lg border border-dashed border-gray-300 p-4">
                        {!! QrCode::size(200)->generate($qrCodeUrl) !!}
                        <p class="mt-2 text-xs text-gray-500">{{ $qrCodeUrl }}</p>
                    </div>

                    <form action="{{ route('two-factor.enable') }}" method="POST" class="space-y-3">
                        @csrf
                        <input type="hidden" name="secret" value="{{ $pendingSecret }}">
                        <flux:input name="code" label="{{ __('Código de verificación') }}" required autocomplete="one-time-code"
                            placeholder="123 456" />
                        @error('code')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror

                        <div class="flex items-center justify-end gap-2">
                            <flux:button variant="primary" type="submit">{{ __('Activar 2FA') }}</flux:button>
                        </div>
                    </form>

                    @if (count($recoveryCodes))
                        <div class="space-y-2">
                            <h4 class="text-sm font-semibold">{{ __('Códigos de recuperación') }}</h4>
                            <p class="text-xs text-gray-500">{{ __('Guárdalos en un lugar seguro. Cada código se puede usar una sola vez.') }}</p>
                            <div class="grid gap-2 rounded-md bg-gray-50 p-3 md:grid-cols-2">
                                @foreach ($recoveryCodes as $code)
                                    <span class="font-mono text-xs tracking-widest">{{ $code }}</span>
                                @endforeach
                            </div>
                            <form method="POST" action="{{ route('two-factor.recovery') }}">
                                @csrf
                                <flux:button type="submit" variant="secondary">{{ __('Regenerar códigos') }}</flux:button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            <div class="space-y-4 rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <h3 class="text-lg font-semibold">{{ __('Passkeys / WebAuthn') }}</h3>
                <p class="text-sm text-gray-600">{{ __('Inicia sesión sin contraseña con claves de acceso ligadas a tu dispositivo.') }}</p>

                <div class="space-y-2">
                    <flux:input name="passkey_name" id="passkey_name" label="{{ __('Nombre del dispositivo') }}" placeholder="MacBook, iPhone" />
                    <flux:button variant="primary" type="button" onclick="registerPasskey()">{{ __('Registrar Passkey') }}</flux:button>
                    <p class="text-xs text-gray-500">{{ __('Se guardará una clave en tu dispositivo y podrás iniciar sesión con ella.') }}</p>
                </div>

                <div class="space-y-2">
                    <h4 class="text-sm font-semibold">{{ __('Passkeys guardadas') }}</h4>
                    <div class="space-y-2">
                        @forelse ($user->passkeys as $passkey)
                            <div class="flex items-center justify-between rounded-md border border-gray-200 p-3">
                                <div>
                                    <p class="text-sm font-medium">{{ $passkey->name }}</p>
                                    <p class="text-xs text-gray-500">{{ __('Último uso:') }} {{ optional($passkey->last_used_at)->diffForHumans() ?? __('Nunca') }}</p>
                                </div>
                                <form action="{{ route('passkeys.destroy', $passkey) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <flux:button type="submit" variant="danger" size="sm">{{ __('Eliminar') }}</flux:button>
                                </form>
                            </div>
                        @empty
                            <p class="text-xs text-gray-500">{{ __('No tienes passkeys registradas aún.') }}</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </x-settings.layout>

    <script>
        function bufferDecode(value) {
            return Uint8Array.from(atob(value), c => c.charCodeAt(0));
        }

        function bufferEncode(value) {
            return btoa(String.fromCharCode(...new Uint8Array(value)));
        }

        async function registerPasskey() {
            if (!window.PublicKeyCredential) {
                alert('Tu navegador no soporta WebAuthn');
                return;
            }

            const response = await fetch('{{ route('passkeys.options') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });

            const options = await response.json();
            const publicKey = options.publicKey ?? options;

            publicKey.challenge = bufferDecode(publicKey.challenge);
            publicKey.user.id = bufferDecode(publicKey.user.id);

            const credential = await navigator.credentials.create({
                publicKey
            });

            const attestation = {
                name: document.getElementById('passkey_name').value,
                id: credential.id,
                rawId: bufferEncode(credential.rawId),
                type: credential.type,
                response: {
                    clientDataJSON: bufferEncode(credential.response.clientDataJSON),
                    attestationObject: bufferEncode(credential.response.attestationObject),
                },
                transports: credential.response.getTransports ? credential.response.getTransports() : [],
            };

            const store = await fetch('{{ route('passkeys.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(attestation)
            });

            if (store.ok) {
                window.location.reload();
            } else {
                alert('No se pudo registrar la passkey');
            }
        }
    </script>
</section>
