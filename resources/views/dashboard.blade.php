<x-layouts.admin title="Panel">
    {{-- Welcome banner --}}
    <section class="relative overflow-hidden rounded-3xl border border-gray-200 bg-gradient-to-br from-gray-100 via-gray-100 to-gray-200/70 dark:border-gray-800 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800/70 px-6 py-8 shadow-sm sm:px-10 sm:py-10">
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute -left-16 -top-24 h-72 w-72 rounded-full bg-yellow-200/20 blur-3xl dark:bg-yellow-500/10"></div>
            <div class="absolute -bottom-32 right-0 h-80 w-80 rounded-full bg-indigo-200/20 blur-3xl dark:bg-indigo-500/15"></div>
            <div class="absolute inset-0 bg-[radial-gradient(rgba(17,24,39,0.06)_1px,transparent_1px)] [background-size:22px_22px] dark:bg-[radial-gradient(rgba(255,255,255,0.05)_1px,transparent_1px)]"></div>
        </div>

        <div class="relative flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-xl">
                <h1 class="text-3xl font-semibold tracking-tight text-gray-900 dark:text-white sm:text-4xl">Hola, {{ str(auth()->user()->name)->before(' ') }}</h1>
                <p class="mt-2 text-base text-gray-600 dark:text-gray-400">
                    @if (auth()->user()->canManageContent())
                        Esto es lo que está pasando en IRSO BLOG. Publica, modera comentarios y administra a tu comunidad desde aquí.
                    @else
                        Escribe nuevas publicaciones y sigue lo que la comunidad comenta sobre ellas.
                    @endif
                </p>
            </div>
            <div class="flex shrink-0 flex-wrap gap-3">
                <a href="{{ route('admin.publications.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-800 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-200">
                    <x-admin.icon name="plus" /> Nueva publicación
                </a>
                <a href="{{ route('blog.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm ring-1 ring-inset ring-gray-200 transition hover:bg-gray-50 hover:text-gray-900 dark:bg-gray-800 dark:text-gray-200 dark:ring-gray-700 dark:hover:bg-gray-700 dark:hover:text-white">
                    <x-admin.icon name="globe" /> Ver blog
                </a>
            </div>
        </div>
    </section>

    {{-- Totals --}}
    <section class="mt-8 grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
        @if (auth()->user()->canManageContent())
            <x-admin.stat-card label="Publicaciones" :value="number_format($stats['publications'])" icon="document" color="yellow"
                :hint="$stats['publicationsThisMonth'].' este mes · Ver todas'" :href="route('admin.publications.index')" />
            <x-admin.stat-card label="Usuarios" :value="number_format($stats['users'])" icon="users" color="indigo"
                :hint="$stats['usersThisMonth'].' nuevos este mes'.(auth()->user()->isAdmin() ? ' · Administrar' : '')"
                :href="auth()->user()->isAdmin() ? route('admin.users.index') : null" />
            <x-admin.stat-card label="Comentarios" :value="number_format($stats['comments'])" icon="chat" color="emerald"
                hint="En todas las publicaciones" />
            <x-admin.stat-card label="Categorías" :value="number_format($stats['categories'])" icon="tag" color="rose"
                hint="Organiza tu contenido" :href="route('admin.categories.index')" />
        @else
            <x-admin.stat-card label="Mis publicaciones" :value="number_format($stats['myPublications'])" icon="document" color="yellow"
                hint="Ver y editar tus publicaciones" :href="route('admin.publications.mine')" />
            <x-admin.stat-card label="Comentarios recibidos" :value="number_format($stats['commentsOnMine'])" icon="chat" color="emerald"
                hint="En tus publicaciones" />
            <x-admin.stat-card label="Publicaciones del blog" :value="number_format($stats['publications'])" icon="globe" color="indigo"
                hint="Léelas en el blog" :href="route('blog.index')" />
            <x-admin.stat-card label="Categorías" :value="number_format($stats['categories'])" icon="tag" color="rose"
                hint="Temas disponibles para publicar" />
        @endif
    </section>

    <div class="mt-8 grid gap-6 xl:grid-cols-3">
        {{-- Latest publications --}}
        <section class="overflow-hidden rounded-2xl border border-gray-200/80 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-sm xl:col-span-2">
            <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 px-5 py-4">
                <div>
                    @if (auth()->user()->canManageContent())
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Publicaciones recientes</h2>
                        <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">Lo último que se publicó en el blog</p>
                    @else
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Tus publicaciones recientes</h2>
                        <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">Lo último que escribiste</p>
                    @endif
                </div>
                <x-admin.button :href="route(auth()->user()->canManageContent() ? 'admin.publications.index' : 'admin.publications.mine')" variant="ghost" size="sm">Ver todas</x-admin.button>
            </div>

            <ul class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse ($latestPublications as $publication)
                    <li class="flex items-center gap-4 px-5 py-4 transition hover:bg-gray-50/60 dark:hover:bg-white/[0.02]">
                        <img src="{{ $publication->src_img_url }}" alt="" class="h-14 w-20 shrink-0 rounded-lg object-cover ring-1 ring-gray-900/5 dark:ring-white/10" />
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-medium text-gray-900 dark:text-white">{{ $publication->title }}</p>
                            <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-500 dark:text-gray-400">
                                @if ($publication->category)
                                    <x-admin.badge color="yellow">{{ $publication->category->name }}</x-admin.badge>
                                @endif
                                <span>{{ $publication->user?->name }}</span>
                                <span class="flex items-center gap-1"><x-admin.icon name="clock" class="h-3.5 w-3.5" />{{ $publication->created_at?->locale('es')->diffForHumans() }}</span>
                            </div>
                        </div>
                        <span class="hidden items-center gap-1 rounded-lg bg-gray-50 px-2.5 py-1 dark:bg-gray-800 text-xs font-medium text-gray-600 dark:text-gray-400 sm:flex">
                            <x-admin.icon name="chat" class="h-4 w-4" /> {{ $publication->comments_count }}
                        </span>
                    </li>
                @empty
                    <li>
                        <x-admin.empty-state icon="document" :title="auth()->user()->canManageContent() ? 'Aún no hay publicaciones' : 'Todavía no has publicado nada'" description="Crea la primera y aparecerá aquí.">
                            <x-admin.button :href="route('admin.publications.create')" icon="plus" size="sm">Nueva publicación</x-admin.button>
                        </x-admin.empty-state>
                    </li>
                @endforelse
            </ul>
        </section>

        <div class="space-y-6">
            {{-- Latest users (administrators only) --}}
            @if ($latestUsers->isNotEmpty())
                <section class="overflow-hidden rounded-2xl border border-gray-200/80 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-sm">
                    <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 px-5 py-4">
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Nuevos usuarios</h2>
                        <x-admin.button :href="route('admin.users.index')" variant="ghost" size="sm">Ver todos</x-admin.button>
                    </div>
                    <ul class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach ($latestUsers as $member)
                            <li class="flex items-center gap-3 px-5 py-3">
                                <x-admin.avatar :name="$member->name" />
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-medium text-gray-900 dark:text-white">{{ $member->name }}</p>
                                    <p class="truncate text-xs text-gray-500 dark:text-gray-400">{{ $member->email }}</p>
                                </div>
                                <x-admin.role-badge :role="$member->role" />
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endif

            {{-- Top categories --}}
            <section class="rounded-2xl border border-gray-200/80 bg-white dark:border-gray-800 dark:bg-gray-900 p-5 shadow-sm">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white">Categorías más usadas</h2>
                @php($max = max(1, $topCategories->max('publications_count')))
                <ul class="mt-4 space-y-4">
                    @forelse ($topCategories as $category)
                        <li>
                            <div class="flex items-center justify-between text-sm">
                                <span class="font-medium text-gray-700 dark:text-gray-300">{{ $category->name }}</span>
                                <span class="text-gray-500 dark:text-gray-400">{{ $category->publications_count }}</span>
                            </div>
                            <div class="mt-1.5 h-2 overflow-hidden rounded-full bg-gray-100 dark:bg-gray-800">
                                <div class="h-full rounded-full bg-gradient-to-r from-yellow-400 to-amber-500" style="width: {{ round($category->publications_count / $max * 100) }}%"></div>
                            </div>
                        </li>
                    @empty
                        <li class="text-sm text-gray-500 dark:text-gray-400">Cuando publiques, verás aquí las categorías con más contenido.</li>
                    @endforelse
                </ul>
            </section>
        </div>
    </div>
</x-layouts.admin>
