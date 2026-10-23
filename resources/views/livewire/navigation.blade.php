<nav class="bg-gray-800" x-data="{ open: false }">
    <div class="mx-auto max-w-7xl px-2 sm:px-6 lg:px-8">
        <div class="relative flex h-16 items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('dashboard') }}" class="flex flex-shrink-0 items-center gap-2 text-white">
                    <x-application-mark class="block h-8 w-auto" />
                    <span class="hidden font-semibold sm:inline">IRSO BLOG</span>
                </a>

                <a href="{{ route('dashboard') }}" class="rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-gray-700 hover:text-white">Panel</a>
                <a href="{{ route('blog.index') }}" class="rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-gray-700 hover:text-white">Blog</a>
            </div>

            @auth
                <div class="relative" x-on:click.outside="open = false">
                    <button type="button" x-on:click="open = ! open" class="flex rounded-full bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800" aria-haspopup="true" x-bind:aria-expanded="open">
                        <span class="sr-only">Abrir menú de usuario</span>
                        <img class="h-8 w-8 rounded-full" src="{{ auth()->user()->profile_photo_url }}" alt="{{ auth()->user()->name }}">
                    </button>

                    <div x-show="open" x-transition x-cloak class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5" role="menu">
                        <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Tu perfil</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Cerrar sesión</button>
                        </form>
                    </div>
                </div>
            @else
                <div class="flex gap-4">
                    <a href="{{ route('login') }}" class="rounded-md bg-gray-900 px-3 py-2 text-sm font-medium text-white">Iniciar sesión</a>
                    <a href="{{ route('register') }}" class="rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-gray-700 hover:text-white">Registrarse</a>
                </div>
            @endauth
        </div>
    </div>
</nav>
