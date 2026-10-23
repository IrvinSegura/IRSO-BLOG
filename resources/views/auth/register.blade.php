<x-layouts.auth :title="__('Crea tu cuenta')" :subtitle="__('Regístrate con tu cuenta favorita o con tu correo.')">
    <form method="POST" action="{{ route('register') }}" class="mt-8 space-y-5">
        @csrf

        <x-auth-input name="name" icon="user" :label="__('Nombre')" :value="old('name')"
            placeholder="{{ __('Tu nombre') }}" required autofocus autocomplete="name" />

        <x-auth-input name="email" type="email" icon="mail" :label="__('Correo')" :value="old('email')"
            placeholder="tu@correo.com" required autocomplete="username" />

        <x-auth-input name="password" type="password" icon="lock" :label="__('Contraseña')"
            placeholder="••••••••" required autocomplete="new-password" />

        <x-auth-input name="password_confirmation" type="password" icon="lock" :label="__('Confirmar contraseña')"
            placeholder="••••••••" required autocomplete="new-password" />

        @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
            <label for="terms" class="flex items-start gap-2 text-sm text-gray-600">
                <input id="terms" type="checkbox" name="terms" required class="mt-0.5 h-4 w-4 rounded border-gray-300 text-gray-900 focus:ring-gray-900/20" />
                <span>
                    {!! __('Acepto los :terms_of_service y la :privacy_policy', [
                        'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="font-medium text-gray-900 underline">'.__('Términos del servicio').'</a>',
                        'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="font-medium text-gray-900 underline">'.__('Política de privacidad').'</a>',
                    ]) !!}
                </span>
            </label>
        @endif

        <x-social-login-buttons class="py-1" />

        <button type="submit"
            class="flex w-full items-center justify-center rounded-lg bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900 focus-visible:ring-offset-2 active:bg-black">
            {{ __('Crear cuenta') }}
        </button>
    </form>

    <p class="mt-8 text-center text-sm text-gray-500">
        {{ __('¿Ya tienes cuenta?') }}
        <a href="{{ route('login') }}" class="font-semibold text-gray-900 hover:underline">{{ __('Inicia sesión') }}</a>
    </p>
</x-layouts.auth>
