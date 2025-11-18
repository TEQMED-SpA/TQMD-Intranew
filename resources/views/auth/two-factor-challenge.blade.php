<x-layouts.auth.card :title="__('Verificación en dos pasos')">
    <div class="flex flex-col gap-4">
        <p class="text-sm text-gray-600 dark:text-gray-300">{{ __('Ingresa el código de tu app TOTP o un código de recuperación para continuar.') }}</p>

        @if ($errors->any())
            <div class="rounded bg-red-50 p-2 text-sm text-red-700 dark:bg-red-900/40 dark:text-red-200">
                {{ $errors->first('code') }}
            </div>
        @endif

        <form method="POST" action="{{ route('two-factor.challenge.store') }}" class="space-y-4">
            @csrf
            <flux:input name="code" label="{{ __('Código') }}" maxlength="8" autocomplete="one-time-code" required />

            <flux:button type="submit" variant="primary" class="w-full">{{ __('Verificar código') }}</flux:button>
        </form>
    </div>
</x-layouts.auth.card>
