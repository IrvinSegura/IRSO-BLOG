<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>IRSO BLOG</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="min-h-screen bg-gray-200 font-sans antialiased">
        <header class="bg-white p-4 shadow-xl" x-data="{ open: false }">
            <div class="mx-auto flex max-w-screen-xl items-center justify-between">
                <a href="{{ route('blog.index') }}" class="text-xl font-semibold">IRSO BLOG</a>

                <button type="button" class="md:hidden" x-on:click="open = ! open">
                    <span class="sr-only">Abrir menú</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>

                <nav class="hidden md:block">
                    <ul class="flex items-center gap-6 font-semibold">
                        <li><a href="{{ route('blog.index') }}" class="border-b-2 border-gray-700 py-1">Blog</a></li>
                        <li><a href="{{ route('dashboard') }}" class="py-1 hover:text-gray-500">Panel</a></li>
                    </ul>
                </nav>
            </div>

            <nav class="mt-4 md:hidden" x-show="open" x-cloak>
                <ul class="space-y-2 font-semibold">
                    <li><a href="{{ route('blog.index') }}">Blog</a></li>
                    <li><a href="{{ route('dashboard') }}">Panel</a></li>
                </ul>
            </nav>
        </header>

        <main class="mx-auto max-w-screen-xl px-4 pb-4 pt-8">
            <h2 class="mb-4 border-b-2 border-yellow-600">
                <span class="bg-yellow-600 px-2 py-1 uppercase tracking-wide text-white">En tendencia:</span>
            </h2>

            <div class="flex flex-col flex-wrap md:-mx-2 md:flex-row">
                @foreach ($trending as $publication)
                    <div class="mb-4 h-72 w-full md:mb-0 md:h-96 md:w-1/2 lg:w-1/4">
                        <div class="group relative block h-72 overflow-hidden md:mx-2 md:h-96">
                            <img src="{{ $publication->src_img_url }}" alt="{{ $publication->title }}" class="absolute z-0 h-72 w-full object-cover md:h-96" />
                            <div class="absolute z-10 h-72 w-full bg-gradient-to-b from-gray-100/10 to-black/80 transition duration-300 group-hover:bg-black group-hover:opacity-90 md:h-96"></div>
                            <div class="absolute inset-x-0 bottom-0 z-30 h-full translate-y-1/2 p-6 transition delay-100 duration-300 group-hover:translate-y-0">
                                <div class="relative h-1/2">
                                    <h3 class="absolute bottom-0 pb-6 text-xl font-bold leading-tight text-white group-hover:underline">
                                        {{ $publication->title }}
                                    </h3>
                                </div>
                                <p class="h-1/2 pb-4 text-white opacity-0 transition duration-300 group-hover:opacity-100">
                                    {{ $publication->content }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="my-8 flex flex-col flex-wrap md:-mx-4 md:flex-row">
                <section class="mb-4 w-full md:w-2/3 lg:mb-0">
                    <div class="md:mx-4">
                        <h2 class="mb-4 border-b-2 border-red-600">
                            <span class="bg-red-600 px-2 py-1 uppercase tracking-wide text-white">Lo último</span>
                        </h2>

                        @forelse ($latest as $publication)
                            <article class="mb-5 flex flex-wrap items-center border-b border-gray-400 pb-5 md:-mx-2">
                                <div class="w-full md:w-3/12">
                                    <img src="{{ $publication->src_img_url }}" alt="{{ $publication->title }}" class="mb-4 w-full md:mx-2 md:mb-0" />
                                </div>
                                <div class="md:w-9/12">
                                    <div class="md:mx-4">
                                        <h3 class="pb-4 text-2xl font-bold leading-tight text-gray-900">{{ $publication->title }}</h3>
                                        <p class="pb-2 text-gray-800">{{ $publication->content }}</p>
                                        <p class="py-1 text-sm italic text-gray-700">
                                            Publicado
                                            <time datetime="{{ $publication->created_at }}">{{ $publication->created_at?->locale('es')->diffForHumans() }}</time>
                                            por {{ $publication->user?->name }}
                                        </p>
                                    </div>
                                </div>
                            </article>
                        @empty
                            <p class="text-gray-600">No hay más publicaciones por ahora.</p>
                        @endforelse
                    </div>
                </section>

                <aside class="mb-4 w-full md:w-1/3 lg:mb-0">
                    <div class="md:mx-4">
                        <h2 class="mb-4 border-b-2 border-blue-600">
                            <span class="bg-blue-600 px-2 py-1 uppercase tracking-wide text-white">Categorías</span>
                        </h2>
                        <ul class="flex flex-wrap gap-2">
                            @foreach ($categories as $category)
                                <li class="rounded-full bg-white px-3 py-1 text-sm text-gray-700 shadow-sm">{{ $category->name }}</li>
                            @endforeach
                        </ul>
                    </div>
                </aside>
            </div>
        </main>

        <footer class="bg-gray-900 py-4">
            <p class="text-center text-lg text-white">&copy; IRSO BLOG</p>
        </footer>

        @livewireScripts
    </body>
</html>
