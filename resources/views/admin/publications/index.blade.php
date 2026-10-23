<x-layouts.admin title="Publicaciones" :description="$publications->total().' publicaciones en el blog'">
    <x-slot name="actions">
        @if (auth()->user()->canManageContent())
            <x-admin.button :href="route('admin.publications.export')" variant="secondary" icon="download">Exportar JSON</x-admin.button>
        @endif
        <x-admin.button :href="route('admin.publications.create')" icon="plus">Nueva publicación</x-admin.button>
    </x-slot>

    <div class="space-y-6">
        @forelse ($publications as $publication)
            <article class="overflow-hidden rounded-2xl border border-gray-200/80 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-sm" x-data="{ showComments: false }">
                <div class="flex flex-col md:flex-row">
                    <div class="relative md:w-80 md:shrink-0 lg:w-96">
                        <img src="{{ $publication->src_img_url }}" alt="{{ $publication->title }}" class="h-56 w-full object-cover md:absolute md:inset-0 md:h-full" />
                        @if ($publication->category)
                            <span class="absolute left-4 top-4 rounded-full bg-white/90 px-3 py-1 text-xs font-semibold text-gray-900 shadow-sm backdrop-blur">
                                {{ $publication->category->name }}
                            </span>
                        @endif
                    </div>

                    <div class="flex flex-1 flex-col p-6">
                        <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                            <x-admin.badge color="emerald" dot>{{ ucfirst($publication->status ?? 'publicado') }}</x-admin.badge>
                            <span>#{{ $publication->id }}</span>
                            <span class="text-gray-300 dark:text-gray-600">·</span>
                            <span class="truncate">/{{ $publication->slug }}</span>
                        </div>

                        <h2 class="mt-3 text-xl font-semibold tracking-tight text-gray-900 dark:text-white">{{ $publication->title }}</h2>
                        <p class="mt-2 line-clamp-3 text-sm leading-relaxed text-gray-600 dark:text-gray-400">{{ $publication->content }}</p>

                        <div class="mt-auto flex flex-wrap items-center justify-between gap-4 pt-6">
                            <div class="flex items-center gap-3">
                                <x-admin.avatar :name="$publication->user?->name" />
                                <div class="text-sm">
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $publication->user?->name ?? 'Autor eliminado' }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        <time datetime="{{ $publication->created_at?->toIso8601String() }}" title="{{ $publication->created_at?->locale('es')->isoFormat('LLLL') }}">
                                            {{ $publication->created_at?->locale('es')->diffForHumans() }}
                                        </time>
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-1">
                                @can('update', $publication)
                                    <x-admin.button :href="route('admin.publications.edit', $publication)" variant="ghost" size="sm" icon="pencil">Editar</x-admin.button>
                                @endcan
                                <button type="button" x-on:click="showComments = ! showComments"
                                    class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 transition hover:bg-gray-100 hover:text-gray-900 dark:hover:bg-gray-800 dark:hover:text-white"
                                    x-bind:class="showComments && 'bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white'">
                                    <x-admin.icon name="chat" class="h-[18px] w-[18px]" />
                                    {{ $publication->comments->count() }} {{ Str::plural('comentario', $publication->comments->count()) }}
                                    <x-admin.icon name="chevron-down" class="h-4 w-4 transition" x-bind:class="showComments && 'rotate-180'" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-100 dark:border-gray-800 bg-gray-50/60 dark:bg-white/[0.02] px-6 py-5" x-show="showComments" x-collapse x-cloak>
                    <ul class="space-y-4">
                        @forelse ($publication->comments as $comment)
                            <li class="flex gap-3">
                                <x-admin.avatar :name="$comment->user?->name" size="sm" class="mt-0.5" />
                                <div class="flex-1 rounded-xl rounded-tl-sm bg-white dark:bg-gray-800 px-4 py-3 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10">
                                    <div class="flex items-center justify-between gap-3 text-xs">
                                        <span class="font-semibold text-gray-900 dark:text-white">{{ $comment->user?->name ?? 'Usuario eliminado' }}</span>
                                        <span class="text-gray-400">{{ $comment->created_at?->locale('es')->diffForHumans() }}</span>
                                    </div>
                                    <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">{{ $comment->comment }}</p>
                                </div>
                            </li>
                        @empty
                            <li class="text-center text-sm text-gray-500 dark:text-gray-400">Aún no hay comentarios. ¡Sé el primero!</li>
                        @endforelse
                    </ul>

                    <form method="POST" action="{{ route('admin.publications.comments.store', $publication) }}" class="mt-5 flex items-center gap-3">
                        @csrf
                        <x-admin.avatar :name="auth()->user()->name" size="sm" />
                        <div class="relative flex-1">
                            <input name="comment" type="text" required maxlength="255" placeholder="Escribe un comentario…"
                                class="block w-full rounded-full border-gray-300 dark:border-gray-700 dark:bg-gray-950 dark:text-white py-2.5 pl-4 pr-12 text-sm shadow-sm placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 dark:focus:border-gray-400 dark:focus:ring-white/10" />
                            <button type="submit" class="absolute inset-y-1 right-1 flex w-9 items-center justify-center rounded-full bg-gray-900 text-white transition hover:bg-gray-700 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-200">
                                <x-admin.icon name="send" class="h-4 w-4" />
                                <span class="sr-only">Comentar</span>
                            </button>
                        </div>
                    </form>
                </div>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-gray-300 bg-white dark:border-gray-700 dark:bg-gray-900">
                <x-admin.empty-state icon="document" title="Aún no hay publicaciones" description="Comparte tu primera historia con tus lectores.">
                    <x-admin.button :href="route('admin.publications.create')" icon="plus">Nueva publicación</x-admin.button>
                </x-admin.empty-state>
            </div>
        @endforelse
    </div>

    <div class="mt-8">{{ $publications->links() }}</div>
</x-layouts.admin>
