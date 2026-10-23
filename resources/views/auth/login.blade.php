<x-layouts.auth :title="__('Inicia sesión')" :subtitle="__('Ingresa con tu cuenta o con tus credenciales.')">
    <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5">
        @csrf

        <x-auth-input name="email" type="email" icon="mail" :label="__('Correo')" :value="old('email')"
            placeholder="tu@correo.com" required autofocus autocomplete="username" />

        <x-auth-input name="password" type="password" icon="lock" :label="__('Contraseña')"
            placeholder="••••••••" required autocomplete="current-password">
            @if (Route::has('password.request'))
                <x-slot name="aside">
                    <a href="{{ route('password.request') }}" class="text-sm font-medium text-yellow-700 hover:text-yellow-800">
                        {{ __('¿Olvidaste tu contraseña?') }}
                    </a>
                </x-slot>
            @endif
        </x-auth-input>

        <label for="remember_me" class="flex items-center gap-2 text-sm text-gray-600">
            <input id="remember_me" type="checkbox" name="remember" class="h-4 w-4 rounded border-gray-300 text-gray-900 focus:ring-gray-900/20" />
            {{ __('Recuérdame') }}
        </label>

        <x-social-login-buttons class="py-1" />

        <button type="submit"
            class="flex w-full items-center justify-center rounded-lg bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900 focus-visible:ring-offset-2 active:bg-black">
            {{ __('Iniciar sesión') }}
        </button>
    </form>

    @if (Route::has('register'))
        <p class="mt-8 text-center text-sm text-gray-500">
            {{ __('¿Aún no tienes cuenta?') }}
            <a href="{{ route('register') }}" class="font-semibold text-gray-900 hover:underline">{{ __('Regístrate') }}</a>
        </p>
    @endif
</x-layouts.auth>
