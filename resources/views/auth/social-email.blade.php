<x-layouts.auth :title="__('Confirma tu correo')"
    :subtitle="$existingAccount
        ? __('Ya existe una cuenta con este correo. Te enviaremos un código para confirmar que es tuya y vincularla con :provider.', ['provider' => $provider])
        : __(':provider no compartió tu correo. Escríbelo y te enviaremos un código para confirmarlo.', ['provider' => $provider])">
    <form method="POST" action="{{ route('social.email.send') }}" class="mt-8 space-y-5">
        @csrf

        <x-auth-input name="email" type="email" icon="mail" :label="__('Correo')" :value="old('email', $email)"
            placeholder="tu@correo.com" required autofocus autocomplete="email" />

        <button type="submit"
            class="flex w-full items-center justify-center rounded-lg bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900 focus-visible:ring-offset-2 active:bg-black">
            {{ __('Enviar código') }}
        </button>
    </form>

    <p class="mt-8 text-center text-sm text-gray-500">
        <a href="{{ route('login') }}" class="font-semibold text-gray-900 hover:underline">{{ __('Volver a iniciar sesión') }}</a>
    </p>
</x-layouts.auth>
