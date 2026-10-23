@props(['title', 'description' => null])

@php
    $user = auth()->user();
    // "show" mirrors the role middleware on each route, so nobody sees links they cannot open.
    $sections = collect([
        'General' => [
            ['label' => 'Panel', 'route' => 'dashboard', 'icon' => 'home', 'show' => true],
            ['label' => 'Estadísticas', 'route' => 'admin.statistics', 'icon' => 'chart', 'show' => $user->canManageContent()],
        ],
        'Contenido' => [
            ['label' => 'Publicaciones', 'route' => 'admin.publications.index', 'icon' => 'document', 'show' => $user->canManageContent()],
            ['label' => 'Mis publicaciones', 'route' => 'admin.publications.mine', 'icon' => 'pencil', 'active' => ['admin.publications.mine', 'admin.publications.edit'], 'show' => true],
            ['label' => 'Crear publicación', 'route' => 'admin.publications.create', 'icon' => 'plus', 'show' => true],
            ['label' => 'Categorías', 'route' => 'admin.categories.index', 'icon' => 'tag', 'show' => $user->canManageContent()],
        ],
        'Usuarios' => [
            ['label' => 'Control de usuarios', 'route' => 'admin.users.index', 'icon' => 'users', 'show' => $user->isAdmin()],
            ['label' => 'Usuarios ocultos', 'route' => 'admin.users.hidden', 'icon' => 'eye-slash', 'show' => $user->isAdmin()],
            ['label' => 'Roles', 'route' => 'admin.roles.index', 'icon' => 'shield', 'show' => $user->isAdmin()],
        ],
        'Sistema' => [
            ['label' => 'Papelera de reciclaje', 'route' => 'admin.recycle-bin', 'icon' => 'trash', 'show' => $user->isAdmin()],
        ],
    ])->map(fn ($items) => array_filter($items, fn ($item) => $item['show']))->filter();
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title }} · IRSO BLOG</title>

        <x-theme-script />

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="bg-gray-50 font-sans text-gray-900 antialiased dark:bg-gray-950 dark:text-gray-100">
        <div
            x-data="{
                sidebarOpen: false,
                sidebarHidden: document.documentElement.classList.contains('sidebar-hidden'),
                toggleSidebar() {
                    this.sidebarHidden = ! this.sidebarHidden;
                    document.documentElement.classList.toggle('sidebar-hidden', this.sidebarHidden);
                    try { localStorage.setItem('irso-sidebar', this.sidebarHidden ? 'hidden' : 'visible') } catch (error) {}
                },
            }"
            x-on:keydown.escape.window="sidebarOpen = false"
            class="min-h-screen lg:flex"
        >
            {{-- Mobile backdrop --}}
            <div x-show="sidebarOpen" x-cloak x-transition.opacity x-on:click="sidebarOpen = false"
                class="fixed inset-0 z-30 bg-gray-950/60 backdrop-blur-sm lg:hidden"></div>

            {{-- On large screens the width collapses to 0 when hidden; the inner column keeps its width so nothing wraps mid-animation. --}}
            <aside
                class="fixed inset-y-0 left-0 z-40 w-72 -translate-x-full overflow-hidden bg-gray-950 text-gray-400 transition-all duration-300 dark:border-r dark:border-white/5 lg:sticky lg:top-0 lg:h-screen lg:shrink-0 lg:translate-x-0 lg:[.sidebar-hidden_&]:w-0 lg:[.sidebar-hidden_&]:border-0"
                x-bind:class="sidebarOpen && '!translate-x-0'"
                x-bind:inert="window.innerWidth >= 1024 && sidebarHidden"
            >
                <div class="pointer-events-none absolute inset-0 overflow-hidden">
                    <div class="absolute -left-20 -top-24 h-64 w-64 rounded-full bg-yellow-500/10 blur-3xl"></div>
                    <div class="absolute -bottom-24 -right-20 h-64 w-64 rounded-full bg-indigo-600/15 blur-3xl"></div>
                </div>

                <div class="relative flex h-full w-72 flex-col">
                    <div class="flex h-16 shrink-0 items-center justify-between px-6">
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 text-base font-semibold tracking-wide text-white">
                            <x-application-mark class="h-8 w-auto" />
                            IRSO BLOG
                        </a>
                        <button type="button" class="rounded-lg p-1.5 text-gray-400 hover:bg-white/5 hover:text-white lg:hidden" x-on:click="sidebarOpen = false">
                            <x-admin.icon name="x" />
                            <span class="sr-only">Cerrar menú</span>
                        </button>
                    </div>

                    <nav class="flex-1 space-y-7 overflow-y-auto px-4 py-6">
                        @foreach ($sections as $section => $items)
                            <div>
                                <p class="px-3 text-[11px] font-semibold uppercase tracking-widest text-gray-500">{{ $section }}</p>
                                <ul class="mt-2 space-y-1">
                                    @foreach ($items as $item)
                                        @php($active = request()->routeIs($item['active'] ?? $item['route']))
                                        <li>
                                            <a href="{{ route($item['route']) }}" @class([
                                                'group relative flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition',
                                                'bg-white/10 text-white' => $active,
                                                'hover:bg-white/5 hover:text-white' => ! $active,
                                            ])>
                                                @if ($active)
                                                    <span class="absolute inset-y-1.5 -left-4 w-1 rounded-r-full bg-yellow-400"></span>
                                                @endif
                                                <x-admin.icon :name="$item['icon']" @class(['h-5 w-5', 'text-yellow-400' => $active, 'text-gray-500 group-hover:text-gray-300' => ! $active]) />
                                                {{ $item['label'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </nav>

                    <div class="border-t border-white/10 p-4">
                        <a href="{{ route('blog.index') }}" class="flex items-center justify-between rounded-xl bg-gradient-to-br from-yellow-500/15 to-indigo-500/10 p-4 ring-1 ring-inset ring-white/10 transition hover:ring-white/20">
                            <div>
                                <p class="text-sm font-semibold text-white">Ver el blog</p>
                                <p class="mt-0.5 text-xs text-gray-400">Así lo ven tus lectores</p>
                            </div>
                            <x-admin.icon name="globe" class="h-6 w-6 text-yellow-400" />
                        </a>
                    </div>
                </div>
            </aside>

            <div class="flex min-w-0 flex-1 flex-col">
                <header class="sticky top-0 z-20 flex h-16 items-center justify-between gap-4 border-b border-gray-200/80 bg-white/80 px-4 backdrop-blur-md dark:border-gray-800 dark:bg-gray-950/80 sm:px-6 lg:px-8">
                    <div class="flex items-center gap-3">
                        <button type="button" class="-ml-1 rounded-lg p-2 text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800 lg:hidden" x-on:click="sidebarOpen = true">
                            <x-admin.icon name="menu" class="h-6 w-6" />
                            <span class="sr-only">Abrir menú</span>
                        </button>
                        <button type="button" x-on:click="toggleSidebar()"
                            class="-ml-2 hidden rounded-lg p-2 text-gray-500 transition hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white lg:inline-flex"
                            x-bind:title="sidebarHidden ? 'Mostrar menú' : 'Ocultar menú'" title="Ocultar menú"
                            x-bind:aria-expanded="(! sidebarHidden).toString()">
                            <x-admin.icon name="sidebar" class="h-5 w-5" />
                            <span class="sr-only" x-text="sidebarHidden ? 'Mostrar menú' : 'Ocultar menú'">Ocultar menú</span>
                        </button>
                        <span class="hidden h-5 w-px bg-gray-200 dark:bg-gray-800 lg:block"></span>
                        <nav class="hidden items-center gap-2 text-sm text-gray-500 dark:text-gray-400 sm:flex" aria-label="Ruta">
                            <a href="{{ route('dashboard') }}" class="hover:text-gray-900 dark:hover:text-white">Panel</a>
                            @unless (request()->routeIs('dashboard'))
                                <span class="text-gray-300 dark:text-gray-700">/</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $title }}</span>
                            @endunless
                        </nav>
                    </div>

                    <div class="relative" x-data="{
                        open: false,
                        dark: document.documentElement.classList.contains('dark'),
                        toggleTheme() {
                            window.irsoSetTheme(! this.dark);
                        },
                    }" x-on:theme-changed.window="dark = $event.detail.dark"
                        x-on:click.outside="open = false" x-on:keydown.escape.window="open = false">
                        <button type="button" class="flex items-center gap-3 rounded-full py-1 pl-1 pr-2 transition hover:bg-gray-100 dark:hover:bg-gray-800" x-on:click="open = ! open">
                            <x-admin.avatar :name="$user->name" />
                            <span class="hidden text-left sm:block">
                                <span class="block text-sm font-semibold leading-tight text-gray-900 dark:text-white">{{ $user->name }}</span>
                                <span class="block text-xs leading-tight text-gray-500 dark:text-gray-400">{{ $user->role?->name ?? 'Usuario' }}</span>
                            </span>
                            <x-admin.icon name="chevron-down" class="hidden h-4 w-4 text-gray-400 sm:block" />
                        </button>

                        <div x-show="open" x-cloak x-transition.origin.top.right
                            class="absolute right-0 mt-2 w-64 overflow-hidden rounded-xl bg-white shadow-lg ring-1 ring-gray-900/5 dark:bg-gray-900 dark:ring-white/10">
                            <div class="border-b border-gray-100 px-4 py-3 dark:border-gray-800">
                                <p class="truncate text-sm font-semibold text-gray-900 dark:text-white">{{ $user->name }}</p>
                                <p class="truncate text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                            </div>
                            <div class="p-1.5">
                                <a href="{{ route('profile.show') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800">
                                    <x-admin.icon name="user" class="h-[18px] w-[18px] text-gray-400" /> Mi perfil
                                </a>
                                <a href="{{ route('blog.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800">
                                    <x-admin.icon name="globe" class="h-[18px] w-[18px] text-gray-400" /> Ver blog
                                </a>
                            </div>
                            <div class="border-t border-gray-100 p-1.5 dark:border-gray-800">
                                <button type="button" role="switch" x-bind:aria-checked="dark.toString()" x-on:click="toggleTheme()"
                                    class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800">
                                    <x-admin.icon name="moon" class="h-[18px] w-[18px] text-gray-400" x-show="! dark" />
                                    <x-admin.icon name="sun" class="h-[18px] w-[18px] text-yellow-400" x-show="dark" x-cloak />
                                    <span class="flex-1 text-left">Tema oscuro</span>
                                    <span class="relative inline-flex h-5 w-9 shrink-0 items-center rounded-full transition-colors"
                                        x-bind:class="dark ? 'bg-yellow-400' : 'bg-gray-200'">
                                        <span class="inline-block h-4 w-4 rounded-full bg-white shadow-sm ring-1 ring-gray-900/5 transition-transform"
                                            x-bind:class="dark ? 'translate-x-[18px]' : 'translate-x-0.5'"></span>
                                    </span>
                                </button>
                            </div>
                            <div class="border-t border-gray-100 p-1.5 dark:border-gray-800">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-500/10">
                                        <x-admin.icon name="logout" class="h-[18px] w-[18px]" /> Cerrar sesión
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </header>

                <main class="mx-auto w-full max-w-7xl flex-1 px-4 py-8 sm:px-6 lg:px-8">
                    <x-admin.flash-message />

                    @unless (request()->routeIs('dashboard'))
                        <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
                            <div>
                                <h1 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">{{ $title }}</h1>
                                @if ($description)
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $description }}</p>
                                @endif
                            </div>
                            @isset($actions)
                                <div class="flex flex-wrap items-center gap-2">{{ $actions }}</div>
                            @endisset
                        </div>
                    @endunless

                    {{ $slot }}
                </main>
            </div>
        </div>

        {{-- Jetstream's profile and API token forms push their dialogs here. --}}
        @stack('modals')
        @stack('scripts')
        @livewireScripts
    </body>
</html>
