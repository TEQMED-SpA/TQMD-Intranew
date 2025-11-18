@extends('components.layouts.auth')

@section('content')
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Autenticación en dos pasos')"
            :description="__('Ingresa el código generado por tu app TOTP para continuar')" />

        @if (session('status'))
            <div class="rounded-md bg-green-50 p-4 text-sm text-green-700">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('two-factor.login') }}" class="flex flex-col gap-6">
            @csrf

            <flux:input name="code" label="{{ __('Código') }}" required autofocus autocomplete="one-time-code"
                placeholder="123 456" />

            @error('code')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror

            <div class="flex items-center justify-end">
                <flux:button variant="primary" type="submit" class="w-full">{{ __('Validar código') }}</flux:button>
            </div>
        </form>
    </div>
@endsection
