@php
    $featured = $trending->first();
    $sideTrending = $trending->skip(1);
    $resultTitle = match (true) {
        $search !== '' && $category !== null => "“{$search}” en {$category->name}",
        $search !== '' => "Resultados para “{$search}”",
        $category !== null => $category->name,
        default => 'Lo último',
    };
@endphp

<x-layouts.blog :title="$filtering ? $resultTitle : null" :search="$search">
    @if ($showTrending)
        {{-- Trending --}}
        <section id="tendencias" class="mx-auto max-w-7xl scroll-mt-20 px-4 pt-10 sm:px-6 lg:px-8">
            <p class="inline-flex items-center gap-1.5 text-xs font-semibold uppercase tracking-widest text-amber-600 dark:text-amber-400">
                <x-admin.icon name="sparkles" class="h-4 w-4" /> En tendencia
            </p>
            <h1 class="mt-2 text-3xl font-extrabold tracking-tight sm:text-4xl">Historias destacadas</h1>

            @if ($featured)
                <div class="mt-8 grid gap-6 lg:grid-cols-5">
                    <article class="group relative isolate flex min-h-[26rem] animate-fade-up overflow-hidden rounded-3xl bg-gray-900 shadow-xl lg:col-span-3">
                        <img src="{{ $featured->src_img_url }}" alt="" class="absolute inset-0 -z-10 h-full w-full object-cover transition duration-700 group-hover:scale-105" />
                        <div class="absolute inset-0 -z-10 bg-gradient-to-t from-gray-950 via-gray-950/50 to-transparent"></div>
                        <div class="mt-auto p-6 sm:p-8">
                            @if ($featured->category)
                                <span class="rounded-full bg-amber-400 px-3 py-1 text-xs font-bold text-gray-950">{{ $featured->category->name }}</span>
                            @endif
                            <h2 class="mt-4 max-w-2xl text-2xl font-bold leading-tight text-white sm:text-3xl">
                                <a href="{{ $featured->url() }}" class="focus:outline-none focus-visible:underline after:absolute after:inset-0 after:content-['']">{{ $featured->title }}</a>
                            </h2>
                            <p class="mt-3 line-clamp-2 max-w-2xl text-gray-300">{{ $featured->content }}</p>
                            <div class="mt-5 flex flex-wrap items-center gap-3 text-sm text-gray-300">
                                <x-admin.avatar :name="$featured->user?->name" size="sm" class="ring-gray-900" />
                                <span class="font-medium text-white">{{ $featured->user?->name }}</span>
                                <span class="text-gray-500">·</span>
                                <time datetime="{{ $featured->created_at?->toIso8601String() }}">{{ $featured->created_at?->locale('es')->diffForHumans() }}</time>
                                <span class="text-gray-500">·</span>
                                <span class="flex items-center gap-1"><x-admin.icon name="heart" class="h-4 w-4" />{{ $featured->reactions_count }}</span>
                                <span class="flex items-center gap-1"><x-admin.icon name="chat" class="h-4 w-4" />{{ $featured->comments_count }}</span>
                            </div>
                        </div>
                    </article>

                    <div class="flex flex-col gap-4 lg:col-span-2">
                        @foreach ($sideTrending as $publication)
                            <article class="group relative flex h-32 animate-fade-up gap-4 rounded-2xl border border-gray-200/80 bg-white p-3 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-gray-800 dark:bg-gray-900 dark:hover:border-gray-700" style="--delay: {{ $loop->iteration * 80 }}ms">
                                <div class="relative w-32 shrink-0 overflow-hidden rounded-xl sm:w-40">
                                    <img src="{{ $publication->src_img_url }}" alt="" loading="lazy" class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-105" />
                                    <span class="absolute left-2 top-2 flex h-7 w-7 items-center justify-center rounded-full bg-white/90 text-xs font-bold text-gray-900 shadow-sm">{{ $loop->iteration + 1 }}</span>
                                </div>
                                <div class="flex min-w-0 flex-col justify-center py-1">
                                    @if ($publication->category)
                                        <span class="text-xs font-semibold uppercase tracking-wide text-amber-600 dark:text-amber-400">{{ $publication->category->name }}</span>
                                    @endif
                                    <h3 class="mt-1 line-clamp-2 font-semibold leading-snug text-gray-900 group-hover:underline dark:text-white">
                                        <a href="{{ $publication->url() }}" class="focus:outline-none after:absolute after:inset-0 after:content-['']">{{ $publication->title }}</a>
                                    </h3>
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
    @endif

    <div @class(['mx-auto grid max-w-7xl gap-12 px-4 pb-20 sm:px-6 lg:grid-cols-3 lg:px-8', 'mt-16' => $showTrending, 'mt-10' => ! $showTrending])>
        {{-- Latest, or the search / category results --}}
        <section id="lo-ultimo" class="scroll-mt-20 lg:col-span-2">
            <div class="flex flex-wrap items-end justify-between gap-3 border-b border-gray-200 pb-4 dark:border-gray-800">
                <div class="min-w-0">
                    @if ($filtering)
                        <a href="{{ route('blog.index') }}" class="inline-flex items-center gap-1 text-sm font-medium text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                            <x-admin.icon name="arrow-left" class="h-4 w-4" /> Volver al inicio
                        </a>
                        <h1 class="mt-2 truncate text-2xl font-bold tracking-tight">{{ $resultTitle }}</h1>
                    @else
                        <h2 class="text-2xl font-bold tracking-tight">Lo último</h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Las publicaciones más recientes</p>
                    @endif
                </div>
                <span class="text-sm text-gray-500 dark:text-gray-400">{{ $latest->total() }} {{ $latest->total() === 1 ? 'publicación' : 'publicaciones' }}</span>
            </div>

            @if ($latest->isEmpty())
                <x-admin.empty-state :icon="$filtering ? 'search' : 'inbox'"
                    :title="$filtering ? 'No encontramos publicaciones' : 'Todavía no hay publicaciones'"
                    :description="$filtering ? 'Prueba con otras palabras o quita el filtro de categoría.' : 'Cuando alguien publique, aparecerá aquí primero.'"
                    class="mt-6 rounded-2xl bg-gray-50 dark:bg-gray-900">
                    @if ($filtering)
                        <x-admin.button :href="route('blog.index')" variant="secondary" size="sm">Ver todas</x-admin.button>
                    @endif
                </x-admin.empty-state>
            @else
                <div class="mt-8 grid gap-8 sm:grid-cols-2">
                    @foreach ($latest as $publication)
                        <x-blog.card :publication="$publication" :index="$loop->index"
                            :saved="$savedIds->has($publication->id)"
                            :badge="! $filtering && $latest->onFirstPage() && $loop->first ? 'Más reciente' : null" />
                    @endforeach
                </div>

                @if ($latest->hasPages())
                    <nav class="mt-10" aria-label="Paginación">{{ $latest->links() }}</nav>
                @endif
            @endif
        </section>

        {{-- Sidebar --}}
        <aside class="space-y-8">
            <section id="categorias" class="scroll-mt-20 rounded-2xl border border-gray-200/80 bg-gray-50/60 p-6 dark:border-gray-800 dark:bg-gray-900">
                <h2 class="text-lg font-bold tracking-tight">Categorías</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Explora por tema</p>
                <ul class="mt-5 flex flex-wrap gap-2">
                    @foreach ($categories->take(14) as $item)
                        @php($active = $category?->is($item))
                        <li>
                            <a href="{{ $active ? route('blog.index', array_filter(['q' => $search])) : route('blog.index', array_filter(['q' => $search, 'categoria' => $item->id])) }}"
                                @class([
                                    'inline-flex items-center gap-1.5 rounded-full border px-3 py-1.5 text-sm shadow-sm transition',
                                    'border-amber-400 bg-amber-400 font-semibold text-gray-950' => $active,
                                    'border-gray-200 bg-white text-gray-700 hover:border-amber-300 hover:text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:border-amber-400/50 dark:hover:text-white' => ! $active,
                                ])
                                @if ($active) aria-current="true" title="Quitar filtro" @endif>
                                {{ $item->name }}
                                @if ($active)
                                    <x-admin.icon name="x" class="h-3.5 w-3.5" />
                                @elseif ($item->publications_count > 0)
                                    <span class="rounded-full bg-amber-100 px-1.5 text-xs font-semibold text-amber-700 dark:bg-amber-400/15 dark:text-amber-300">{{ $item->publications_count }}</span>
                                @endif
                            </a>
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
</x-layouts.blog>
