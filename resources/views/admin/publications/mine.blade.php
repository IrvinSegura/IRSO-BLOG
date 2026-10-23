<x-layouts.admin title="Mis publicaciones" :description="'Todo lo que has escrito ('.$publications->total().'). Edítalas o revisa sus comentarios.'">
    @if ($publications->isEmpty())
        <div class="rounded-2xl border border-dashed border-gray-300 bg-white dark:border-gray-700 dark:bg-gray-900">
            <x-admin.empty-state icon="document" title="Todavía no has publicado nada" description="Escribe tu primera historia; aparecerá aquí y en el blog.">
                <x-admin.button :href="route('admin.publications.create')" icon="plus">Crear mi primera publicación</x-admin.button>
            </x-admin.empty-state>
        </div>
    @else
        <ul class="space-y-4">
            @foreach ($publications as $publication)
                <li class="overflow-hidden rounded-2xl border border-gray-200/80 bg-white shadow-sm transition hover:shadow-md dark:border-gray-800 dark:bg-gray-900 dark:hover:border-gray-700"
                    x-data="{ showComments: false }">
                    <div class="flex flex-col gap-4 p-4 sm:flex-row sm:items-center">
                        <img src="{{ $publication->src_img_url }}" alt="{{ $publication->title }}"
                            class="h-40 w-full shrink-0 rounded-xl object-cover ring-1 ring-gray-900/5 dark:ring-white/10 sm:h-24 sm:w-36" />

                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2 text-xs">
                                @if ($publication->category)
                                    <x-admin.badge color="yellow">{{ $publication->category->name }}</x-admin.badge>
                                @endif
                                <x-admin.badge color="emerald" dot>{{ ucfirst($publication->status ?? 'publicado') }}</x-admin.badge>
                            </div>
                            <h2 class="mt-2 truncate text-base font-semibold text-gray-900 dark:text-white">{{ $publication->title }}</h2>
                            <p class="mt-1 line-clamp-1 text-sm text-gray-500 dark:text-gray-400">{{ $publication->content }}</p>
                            <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-gray-500 dark:text-gray-400">
                                <span class="flex items-center gap-1">
                                    <x-admin.icon name="clock" class="h-3.5 w-3.5" />
                                    <time datetime="{{ $publication->created_at?->toIso8601String() }}" title="{{ $publication->created_at?->locale('es')->isoFormat('LLLL') }}">
                                        {{ $publication->created_at?->locale('es')->diffForHumans() }}
                                    </time>
                                </span>
                                @if ($publication->updated_at && $publication->created_at && $publication->updated_at->gt($publication->created_at->copy()->addMinute()))
                                    <span class="flex items-center gap-1">
                                        <x-admin.icon name="pencil" class="h-3.5 w-3.5" />
                                        Editada {{ $publication->updated_at->locale('es')->diffForHumans() }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="flex shrink-0 flex-wrap gap-2">
                            <button type="button" x-on:click="showComments = ! showComments"
                                class="inline-flex items-center gap-2 rounded-lg px-3 py-1.5 text-xs font-semibold text-gray-600 transition hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white"
                                x-bind:class="showComments && 'bg-gray-100 text-gray-900 dark:bg-gray-800 dark:text-white'"
                                x-bind:aria-expanded="showComments.toString()">
                                <x-admin.icon name="chat" class="h-4 w-4" />
                                {{ $publication->comments_count }} {{ $publication->comments_count === 1 ? 'comentario' : 'comentarios' }}
                                <x-admin.icon name="chevron-down" class="h-3.5 w-3.5 transition" x-bind:class="showComments && 'rotate-180'" />
                            </button>
                            <x-admin.button :href="route('admin.publications.edit', $publication)" variant="secondary" size="sm" icon="pencil">Editar</x-admin.button>
                        </div>
                    </div>

                    {{-- Comments readers left on this publication, with a box to answer them. --}}
                    <div class="border-t border-gray-100 bg-gray-50/60 px-5 py-5 dark:border-gray-800 dark:bg-white/[0.02]" x-show="showComments" x-collapse x-cloak>
                        <ul class="space-y-4">
                            @forelse ($publication->comments as $comment)
                                <li class="flex gap-3">
                                    <x-admin.avatar :name="$comment->user?->name" size="sm" class="mt-0.5" />
                                    <div class="flex-1 rounded-xl rounded-tl-sm bg-white px-4 py-3 shadow-sm ring-1 ring-gray-900/5 dark:bg-gray-800 dark:ring-white/10">
                                        <div class="flex items-center justify-between gap-3 text-xs">
                                            <span class="font-semibold text-gray-900 dark:text-white">
                                                {{ $comment->user?->name ?? 'Usuario eliminado' }}
                                                @if ($comment->user_id === auth()->id())
                                                    <span class="ml-1 font-normal text-gray-400">(tú)</span>
                                                @endif
                                            </span>
                                            <span class="text-gray-400">{{ $comment->created_at?->locale('es')->diffForHumans() }}</span>
                                        </div>
                                        <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">{{ $comment->comment }}</p>
                                    </div>
                                </li>
                            @empty
                                <li class="text-center text-sm text-gray-500 dark:text-gray-400">Todavía nadie ha comentado esta publicación.</li>
                            @endforelse
                        </ul>

                        <form method="POST" action="{{ route('admin.publications.comments.store', $publication) }}" class="mt-5 flex items-center gap-3">
                            @csrf
                            <x-admin.avatar :name="auth()->user()->name" size="sm" />
                            <div class="relative flex-1">
                                <input name="comment" type="text" required maxlength="255" placeholder="Responde a tus lectores…"
                                    class="block w-full rounded-full border-gray-300 py-2.5 pl-4 pr-12 text-sm shadow-sm placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 dark:border-gray-700 dark:bg-gray-950 dark:text-white dark:focus:border-gray-400 dark:focus:ring-white/10" />
                                <button type="submit" class="absolute inset-y-1 right-1 flex w-9 items-center justify-center rounded-full bg-gray-900 text-white transition hover:bg-gray-700 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-200">
                                    <x-admin.icon name="send" class="h-4 w-4" />
                                    <span class="sr-only">Comentar</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </li>
            @endforeach
        </ul>

        <div class="mt-8">{{ $publications->links() }}</div>
    @endif
</x-layouts.admin>
