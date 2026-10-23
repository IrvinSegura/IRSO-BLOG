@props(['title'])

@php
    $menu = [
        ['label' => 'Panel', 'route' => 'dashboard'],
        ['label' => 'Perfil', 'route' => 'profile.show'],
        ['label' => 'Control de usuarios', 'route' => 'admin.users.index'],
        ['label' => 'Usuarios ocultos', 'route' => 'admin.users.hidden'],
        ['label' => 'Publicaciones', 'route' => 'admin.publications.index'],
        ['label' => 'Categorías', 'route' => 'admin.categories.index'],
        ['label' => 'Roles', 'route' => 'admin.roles.index'],
        ['label' => 'Estadísticas', 'route' => 'admin.statistics'],
        ['label' => 'Papelera de reciclaje', 'route' => 'admin.recycle-bin'],
        ['label' => 'Ver blog', 'route' => 'blog.index'],
    ];
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title }} · {{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-sans antialiased bg-gray-100 text-gray-900">
        <div x-data="{ sidebarOpen: false }" class="min-h-screen md:flex">
            <aside
                class="fixed inset-y-0 left-0 z-40 w-64 bg-gray-800 text-gray-300 transition-transform md:static md:translate-x-0"
                x-bind:class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            >
                <a href="{{ route('dashboard') }}" class="flex h-16 items-center gap-2 px-6 text-lg font-semibold text-white">
                    <x-application-mark class="block h-8 w-auto" />
                    IRSO BLOG
                </a>

                <nav class="space-y-1 px-3 py-4">
                    @foreach ($menu as $item)
                        <a
                            href="{{ route($item['route']) }}"
                            @class([
                                'block rounded-md px-3 py-2 text-sm font-medium',
                                'bg-gray-900 text-white' => request()->routeIs($item['route']),
                                'hover:bg-gray-700 hover:text-white' => ! request()->routeIs($item['route']),
                            ])
                        >
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </nav>
            </aside>

            <div class="flex-1">
                <header class="flex h-16 items-center justify-between bg-white px-4 shadow sm:px-6">
                    <div class="flex items-center gap-3">
                        <button type="button" class="rounded-md p-2 text-gray-500 hover:bg-gray-100 md:hidden" x-on:click="sidebarOpen = ! sidebarOpen">
                            <span class="sr-only">Abrir menú</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                        </button>
                        <h1 class="text-xl font-semibold text-gray-800">{{ $title }}</h1>
                    </div>

                    <div class="flex items-center gap-4 text-sm">
                        <span class="hidden text-gray-600 sm:inline">{{ auth()->user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-gray-600 underline hover:text-gray-900">Cerrar sesión</button>
                        </form>
                    </div>
                </header>

                <main class="p-4 sm:p-6">
                    <x-admin.flash-message />

                    {{ $slot }}
                </main>
            </div>
        </div>

        @stack('scripts')
        @livewireScripts
    </body>
</html>
