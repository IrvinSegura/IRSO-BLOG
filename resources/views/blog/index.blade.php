@php
    $featured = $trending->first();
    $sideTrending = $trending->skip(1);
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>IRSO BLOG · Ideas, historias y lo que te inspira</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <x-theme-script />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="min-h-screen bg-white font-sans text-gray-900 antialiased dark:bg-gray-950 dark:text-gray-100">
        <header class="sticky top-0 z-30 border-b border-gray-200/70 bg-white/80 backdrop-blur-md dark:border-gray-800 dark:bg-gray-950/80" x-data="{ open: false }">
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <a href="{{ route('blog.index') }}" class="flex items-center gap-2.5 text-lg font-bold tracking-tight">
                    <x-application-mark class="h-8 w-auto" />
                    IRSO BLOG
                </a>

                <nav class="hidden items-center gap-8 text-sm font-medium text-gray-600 dark:text-gray-400 md:flex">
                    <a href="#tendencias" class="transition hover:text-gray-900 dark:hover:text-white">Tendencias</a>
                    <a href="#lo-ultimo" class="transition hover:text-gray-900 dark:hover:text-white">Lo último</a>
                    <a href="#categorias" class="transition hover:text-gray-900 dark:hover:text-white">Categorías</a>
                </nav>

                <div class="flex items-center gap-2">
                    <a href="{{ route('dashboard') }}" class="hidden items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-800 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-200 sm:inline-flex">
                        <x-admin.icon name="home" class="h-4 w-4" /> Ir al panel
                    </a>
                    <div class="ml-1 flex items-center gap-2 border-l border-gray-200 pl-3 dark:border-gray-800">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400" aria-hidden="true">Tema:</span>
                        <x-theme-toggle />
                    </div>
                    <button type="button" class="rounded-lg p-2 text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800 md:hidden" x-on:click="open = ! open">
                        <x-admin.icon name="menu" class="h-6 w-6" />
                        <span class="sr-only">Abrir menú</span>
                    </button>
                </div>
            </div>

            <nav class="border-t border-gray-100 px-4 py-3 dark:border-gray-800 md:hidden" x-show="open" x-cloak x-collapse>
                <ul class="space-y-1 text-sm font-medium">
                    <li><a href="#tendencias" class="block rounded-lg px-3 py-2 hover:bg-gray-50 dark:hover:bg-gray-800" x-on:click="open = false">Tendencias</a></li>
                    <li><a href="#lo-ultimo" class="block rounded-lg px-3 py-2 hover:bg-gray-50 dark:hover:bg-gray-800" x-on:click="open = false">Lo último</a></li>
                    <li><a href="#categorias" class="block rounded-lg px-3 py-2 hover:bg-gray-50 dark:hover:bg-gray-800" x-on:click="open = false">Categorías</a></li>
                    <li><a href="{{ route('dashboard') }}" class="block rounded-lg px-3 py-2 hover:bg-gray-50 dark:hover:bg-gray-800">Ir al panel</a></li>
                </ul>
            </nav>
        </header>

        <main>
            {{-- Trending --}}
            <section id="tendencias" class="mx-auto max-w-7xl scroll-mt-20 px-4 pt-10 sm:px-6 lg:px-8">
                <div class="flex items-end justify-between">
                    <div>
                        <p class="inline-flex items-center gap-1.5 text-xs font-semibold uppercase tracking-widest text-amber-600 dark:text-amber-400">
                            <x-admin.icon name="sparkles" class="h-4 w-4" /> En tendencia
                        </p>
                        <h1 class="mt-2 text-3xl font-extrabold tracking-tight sm:text-4xl">Historias destacadas</h1>
                    </div>
                </div>

                @if ($featured)
                    <div class="mt-8 grid gap-6 lg:grid-cols-5">
                        <article class="group relative isolate flex min-h-[26rem] overflow-hidden rounded-3xl bg-gray-900 shadow-xl lg:col-span-3">
                            <img src="{{ $featured->src_img_url }}" alt="{{ $featured->title }}" class="absolute inset-0 -z-10 h-full w-full object-cover transition duration-700 group-hover:scale-105" />
                            <div class="absolute inset-0 -z-10 bg-gradient-to-t from-gray-950 via-gray-950/50 to-transparent"></div>
                            <div class="mt-auto p-6 sm:p-8">
                                @if ($featured->category)
                                    <span class="rounded-full bg-amber-400 px-3 py-1 text-xs font-bold text-gray-950">{{ $featured->category->name }}</span>
                                @endif
                                <h2 class="mt-4 max-w-2xl text-2xl font-bold leading-tight text-white sm:text-3xl">{{ $featured->title }}</h2>
                                <p class="mt-3 line-clamp-2 max-w-2xl text-gray-300">{{ $featured->content }}</p>
                                <div class="mt-5 flex items-center gap-3 text-sm text-gray-300">
                                    <x-admin.avatar :name="$featured->user?->name" size="sm" class="ring-gray-900" />
                                    <span class="font-medium text-white">{{ $featured->user?->name }}</span>
                                    <span class="text-gray-500">·</span>
                                    <time datetime="{{ $featured->created_at?->toIso8601String() }}">{{ $featured->created_at?->locale('es')->diffForHumans() }}</time>
                                </div>
                            </div>
                        </article>

                        <div class="flex flex-col gap-4 lg:col-span-2">
                            @foreach ($sideTrending as $publication)
                                <article class="group flex h-32 gap-4 rounded-2xl border border-gray-200/80 bg-white p-3 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-gray-800 dark:bg-gray-900 dark:hover:border-gray-700">
                                    <div class="relative w-32 shrink-0 overflow-hidden rounded-xl sm:w-40">
                                        <img src="{{ $publication->src_img_url }}" alt="{{ $publication->title }}" class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-105" />
                                        <span class="absolute left-2 top-2 flex h-7 w-7 items-center justify-center rounded-full bg-white/90 text-xs font-bold text-gray-900 shadow-sm">{{ $loop->iteration + 1 }}</span>
                                    </div>
                                    <div class="flex min-w-0 flex-col justify-center py-1">
                                        @if ($publication->category)
                                            <span class="text-xs font-semibold uppercase tracking-wide text-amber-600 dark:text-amber-400">{{ $publication->category->name }}</span>
                                        @endif
                                        <h3 class="mt-1 line-clamp-2 font-semibold leading-snug text-gray-900 group-hover:underline dark:text-white">{{ $publication->title }}</h3>
                                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ $publication->user?->name }} · {{ $publication->created_at?->locale('es')->diffForHumans() }}</p>
                                    </div>
                                </article>
                            @endforeach

                            @if ($sideTrending->isEmpty())
                                <div class="flex flex-1 flex-col items-center justify-center rounded-2xl border border-dashed border-gray-300 p-8 text-center dark:border-gray-700">
                                    <x-admin.icon name="document" class="h-8 w-8 text-gray-300 dark:text-gray-600" />
                                    <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">Pronto habrá más publicaciones en tendencia.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="mt-8 rounded-3xl border border-dashed border-gray-300 dark:border-gray-700">
                        <x-admin.empty-state icon="document" title="Todavía no hay publicaciones" description="Vuelve pronto: aquí aparecerán las historias más recientes." />
                    </div>
                @endif
            </section>

            <div class="mx-auto mt-16 grid max-w-7xl gap-12 px-4 pb-20 sm:px-6 lg:grid-cols-3 lg:px-8">
                {{-- Latest --}}
                <section id="lo-ultimo" class="scroll-mt-20 lg:col-span-2">
                    <div class="flex items-center justify-between border-b border-gray-200 pb-4 dark:border-gray-800">
                        <div>
                            <h2 class="text-2xl font-bold tracking-tight">Lo último</h2>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Las publicaciones más recientes</p>
                        </div>
                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ $latest->count() }} {{ $latest->count() === 1 ? 'publicación' : 'publicaciones' }}</span>
                    </div>

                    @if ($latest->isEmpty())
                        <x-admin.empty-state icon="inbox" title="Todavía no hay publicaciones" description="Cuando alguien publique, aparecerá aquí primero." class="mt-6 rounded-2xl bg-gray-50 dark:bg-gray-900" />
                    @else
                        <div class="mt-8 grid gap-8 sm:grid-cols-2">
                            @foreach ($latest as $publication)
                                <article class="group flex flex-col">
                                    <div class="relative aspect-[16/10] overflow-hidden rounded-2xl bg-gray-100 dark:bg-gray-800">
                                        <img src="{{ $publication->src_img_url }}" alt="{{ $publication->title }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" />
                                        @if ($publication->category)
                                            <span class="absolute left-3 top-3 rounded-full bg-white/90 px-3 py-1 text-xs font-semibold text-gray-900 shadow-sm backdrop-blur">{{ $publication->category->name }}</span>
                                        @endif
                                        @if ($loop->first)
                                            <span class="absolute right-3 top-3 inline-flex items-center gap-1 rounded-full bg-amber-400 px-3 py-1 text-xs font-bold text-gray-950 shadow-sm">
                                                <x-admin.icon name="sparkles" class="h-3.5 w-3.5" /> Más reciente
                                            </span>
                                        @endif
                                    </div>
                                    <h3 class="mt-4 text-lg font-semibold leading-snug text-gray-900 group-hover:underline dark:text-white">{{ $publication->title }}</h3>
                                    <p class="mt-2 line-clamp-2 text-sm text-gray-600 dark:text-gray-400">{{ $publication->content }}</p>
                                    <div class="mt-4 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                                        <span class="flex items-center gap-2">
                                            <x-admin.avatar :name="$publication->user?->name" size="sm" />
                                            <span class="font-medium text-gray-700 dark:text-gray-300">{{ $publication->user?->name }}</span>
                                        </span>
                                        <span class="flex items-center gap-3">
                                            <span>{{ $publication->created_at?->locale('es')->diffForHumans() }}</span>
                                            <span class="flex items-center gap-1"><x-admin.icon name="chat" class="h-4 w-4" />{{ $publication->comments_count }}</span>
                                        </span>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </section>

                {{-- Sidebar --}}
                <aside class="space-y-8">
                    <section id="categorias" class="scroll-mt-20 rounded-2xl border border-gray-200/80 bg-gray-50/60 p-6 dark:border-gray-800 dark:bg-gray-900">
                        <h2 class="text-lg font-bold tracking-tight">Categorías</h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Explora por tema</p>
                        <ul class="mt-5 flex flex-wrap gap-2">
                            @foreach ($categories->take(14) as $category)
                                <li class="inline-flex items-center gap-1.5 rounded-full border border-gray-200 bg-white px-3 py-1.5 text-sm text-gray-700 shadow-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                    {{ $category->name }}
                                    @if ($category->publications_count > 0)
                                        <span class="rounded-full bg-amber-100 px-1.5 text-xs font-semibold text-amber-700 dark:bg-amber-400/15 dark:text-amber-300">{{ $category->publications_count }}</span>
                                    @endif
                                </li>
                            @endforeach
                            @if ($categories->count() > 14)
                                <li class="px-2 py-1.5 text-sm text-gray-500 dark:text-gray-400">y {{ $categories->count() - 14 }} más</li>
                            @endif
                        </ul>
                    </section>

                    <section class="relative overflow-hidden rounded-2xl bg-gray-950 p-6 text-white dark:ring-1 dark:ring-white/10">
                        <div class="pointer-events-none absolute -right-10 -top-10 h-40 w-40 rounded-full bg-amber-500/30 blur-3xl"></div>
                        <div class="pointer-events-none absolute -bottom-12 -left-8 h-40 w-40 rounded-full bg-indigo-600/30 blur-3xl"></div>
                        <div class="relative">
                            <x-application-mark class="h-9 w-auto" />
                            <h2 class="mt-4 text-lg font-bold">¿Tienes algo que contar?</h2>
                            <p class="mt-2 text-sm text-gray-400">Comparte tus ideas con la comunidad de IRSO BLOG.</p>
                            <a href="{{ route('admin.publications.create') }}" class="mt-5 inline-flex items-center gap-2 rounded-lg bg-amber-400 px-4 py-2 text-sm font-semibold text-gray-950 transition hover:bg-amber-300">
                                Escribir publicación <x-admin.icon name="arrow-right" class="h-4 w-4" />
                            </a>
                        </div>
                    </section>
                </aside>
            </div>
        </main>

        <footer class="border-t border-gray-200 bg-gray-50 dark:border-gray-800 dark:bg-gray-900">
            <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-4 px-4 py-8 text-sm text-gray-500 dark:text-gray-400 sm:flex-row sm:px-6 lg:px-8">
                <a href="{{ route('blog.index') }}" class="flex items-center gap-2 font-semibold text-gray-900 dark:text-white">
                    <x-application-mark class="h-6 w-auto" /> IRSO BLOG
                </a>
                <p>&copy; {{ date('Y') }} IRSO BLOG. Ideas, historias y lo que te inspira.</p>
                <a href="#tendencias" class="flex items-center gap-1 hover:text-gray-900 dark:hover:text-white">Volver arriba <x-admin.icon name="arrow-right" class="h-4 w-4 -rotate-90" /></a>
            </div>
        </footer>

        @livewireScripts
    </body>
</html>
