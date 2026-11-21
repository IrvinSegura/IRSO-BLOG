{{-- Public face of the blog: header with search, toasts and footer shared by every blog page. --}}
@props(['title' => null, 'description' => 'Ideas, historias y lo que te inspira', 'search' => ''])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="{{ $description }}">

        <title>{{ $title ? $title.' · IRSO BLOG' : 'IRSO BLOG · Ideas, historias y lo que te inspira' }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <x-theme-script />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="min-h-screen bg-white font-sans text-gray-900 antialiased dark:bg-gray-950 dark:text-gray-100">
        <a href="#contenido" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:rounded-lg focus:bg-white focus:px-4 focus:py-2 focus:shadow-lg dark:focus:bg-gray-900">Saltar al contenido</a>

        <header class="sticky top-0 z-30 border-b border-gray-200/70 bg-white/80 backdrop-blur-md dark:border-gray-800 dark:bg-gray-950/80" x-data="{ open: false }">
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
                <a href="{{ route('blog.index') }}" class="flex shrink-0 items-center gap-2.5 text-lg font-bold tracking-tight">
                    <x-application-mark class="h-8 w-auto" />
                    <span class="hidden sm:inline">IRSO BLOG</span>
                </a>

                <form action="{{ route('blog.index') }}" method="GET" role="search" class="relative hidden max-w-md flex-1 md:block">
                    <label for="blog-search" class="sr-only">Buscar publicaciones</label>
                    <x-admin.icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                    <input id="blog-search" name="q" type="search" value="{{ $search }}" maxlength="100"
                        placeholder="Buscar por título, tema o autor…"
                        class="w-full rounded-full border-gray-200 bg-gray-50 py-2 pl-9 pr-4 text-sm shadow-none transition placeholder:text-gray-400 focus:border-amber-400 focus:bg-white focus:ring-amber-400 dark:border-gray-800 dark:bg-gray-900 dark:focus:bg-gray-900" />
                </form>

                <div class="flex items-center gap-2">
                    <a href="{{ route('blog.saved') }}" @class([
                        'hidden items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium transition sm:inline-flex',
                        'bg-amber-50 text-amber-700 dark:bg-amber-400/10 dark:text-amber-300' => request()->routeIs('blog.saved'),
                        'text-gray-600 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white' => ! request()->routeIs('blog.saved'),
                    ])>
                        <x-admin.icon name="bookmark" class="h-4 w-4" /> Guardados
                    </a>
                    <a href="{{ route('dashboard') }}" class="hidden items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-800 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-200 lg:inline-flex">
                        <x-admin.icon name="home" class="h-4 w-4" /> Ir al panel
                    </a>
                    <div class="ml-1 flex items-center gap-2 border-l border-gray-200 pl-3 dark:border-gray-800">
                        <span class="hidden text-sm font-medium text-gray-600 dark:text-gray-400 xl:inline" aria-hidden="true">Tema:</span>
                        <x-theme-toggle />
                    </div>
                    <button type="button" class="rounded-lg p-2 text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800 md:hidden" x-on:click="open = ! open" x-bind:aria-expanded="open">
                        <x-admin.icon name="menu" class="h-6 w-6" />
                        <span class="sr-only">Abrir menú</span>
                    </button>
                </div>
            </div>

            <nav class="border-t border-gray-100 px-4 py-3 dark:border-gray-800 md:hidden" x-show="open" x-cloak x-collapse>
                <form action="{{ route('blog.index') }}" method="GET" role="search" class="relative mb-3">
                    <label for="blog-search-mobile" class="sr-only">Buscar publicaciones</label>
                    <x-admin.icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                    <input id="blog-search-mobile" name="q" type="search" value="{{ $search }}" maxlength="100" placeholder="Buscar publicaciones…"
                        class="w-full rounded-full border-gray-200 bg-gray-50 py-2 pl-9 pr-4 text-sm focus:border-amber-400 focus:ring-amber-400 dark:border-gray-800 dark:bg-gray-900" />
                </form>
                <ul class="space-y-1 text-sm font-medium">
                    <li><a href="{{ route('blog.index') }}" class="block rounded-lg px-3 py-2 hover:bg-gray-50 dark:hover:bg-gray-800">Inicio del blog</a></li>
                    <li><a href="{{ route('blog.saved') }}" class="block rounded-lg px-3 py-2 hover:bg-gray-50 dark:hover:bg-gray-800">Guardados</a></li>
                    <li><a href="{{ route('dashboard') }}" class="block rounded-lg px-3 py-2 hover:bg-gray-50 dark:hover:bg-gray-800">Ir al panel</a></li>
                </ul>
            </nav>
        </header>

        <x-admin.flash-message />

        <main id="contenido">
            {{ $slot }}
        </main>

        <footer class="border-t border-gray-200 bg-gray-50 dark:border-gray-800 dark:bg-gray-900">
            <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-4 px-4 py-8 text-sm text-gray-500 dark:text-gray-400 sm:flex-row sm:px-6 lg:px-8">
                <a href="{{ route('blog.index') }}" class="flex items-center gap-2 font-semibold text-gray-900 dark:text-white">
                    <x-application-mark class="h-6 w-auto" /> IRSO BLOG
                </a>
                <p>&copy; {{ date('Y') }} IRSO BLOG. Ideas, historias y lo que te inspira.</p>
                <a href="#contenido" class="flex items-center gap-1 hover:text-gray-900 dark:hover:text-white">Volver arriba <x-admin.icon name="arrow-right" class="h-4 w-4 -rotate-90" /></a>
            </div>
        </footer>

        @livewireScripts
    </body>
</html>
