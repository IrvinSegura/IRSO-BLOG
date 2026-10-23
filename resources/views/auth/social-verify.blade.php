<x-layouts.auth :title="__('Revisa tu correo')"
    :subtitle="__('Escribe el código de 6 dígitos que enviamos a :email.', ['email' => $email])">
    <form method="POST" action="{{ route('social.verify.check') }}" class="mt-8 space-y-5">
        @csrf

        <x-auth-input name="code" icon="lock" :label="__('Código')" placeholder="000000"
            inputmode="numeric" pattern="[0-9]{6}" maxlength="6" class="tracking-[0.5em]"
            required autofocus autocomplete="one-time-code" />

        <button type="submit"
            class="flex w-full items-center justify-center rounded-lg bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900 focus-visible:ring-offset-2 active:bg-black">
            {{ __('Verificar y entrar') }}
        </button>
    </form>

    <div class="mt-8 flex items-center justify-between text-sm">
        <form method="POST" action="{{ route('social.email.send') }}">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}" />
            <button type="submit" class="font-semibold text-gray-900 hover:underline">{{ __('Reenviar código') }}</button>
        </form>

        <a href="{{ route('social.email') }}" class="text-gray-500 hover:text-gray-900">{{ __('Usar otro correo') }}</a>
    </div>
</x-layouts.auth>
