{{-- A publication in a grid. The title link stretches over the whole card, so the card is one big target. --}}
@props(['publication', 'index' => 0, 'saved' => false, 'badge' => null])

<article {{ $attributes->class('group relative flex animate-fade-up flex-col') }} style="--delay: {{ min($index, 8) * 60 }}ms">
    <div class="relative aspect-[16/10] overflow-hidden rounded-2xl bg-gray-100 dark:bg-gray-800">
        <img src="{{ $publication->src_img_url }}" alt="" loading="lazy" decoding="async"
            class="h-full w-full object-cover transition duration-500 group-hover:scale-105" />
        @if ($publication->category)
            <span class="absolute left-3 top-3 rounded-full bg-white/90 px-3 py-1 text-xs font-semibold text-gray-900 shadow-sm backdrop-blur">{{ $publication->category->name }}</span>
        @endif
        @if ($badge)
            <span class="absolute right-3 top-3 inline-flex items-center gap-1 rounded-full bg-amber-400 px-3 py-1 text-xs font-bold text-gray-950 shadow-sm">
                <x-admin.icon name="sparkles" class="h-3.5 w-3.5" /> {{ $badge }}
            </span>
        @elseif ($saved)
            <span class="absolute right-3 top-3 flex h-8 w-8 items-center justify-center rounded-full bg-white/90 text-amber-600 shadow-sm" title="Guardada">
                <x-admin.icon name="bookmark" class="h-4 w-4 fill-current" />
                <span class="sr-only">Guardada</span>
            </span>
        @endif
    </div>
    <h3 class="mt-4 text-lg font-semibold leading-snug text-gray-900 dark:text-white">
        <a href="{{ $publication->url() }}" class="rounded focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-400 group-hover:underline after:absolute after:inset-0 after:content-['']">
            {{ $publication->title }}
        </a>
    </h3>
    <p class="mt-2 line-clamp-2 text-sm text-gray-600 dark:text-gray-400">{{ $publication->content }}</p>
    <div class="mt-4 flex items-center justify-between gap-3 text-xs text-gray-500 dark:text-gray-400">
        <span class="flex min-w-0 items-center gap-2">
            <x-admin.avatar :name="$publication->user?->name" size="sm" />
            <span class="truncate font-medium text-gray-700 dark:text-gray-300">{{ $publication->user?->name }}</span>
        </span>
        <span class="flex shrink-0 items-center gap-3">
            <time datetime="{{ $publication->created_at?->toIso8601String() }}">{{ $publication->created_at?->locale('es')->diffForHumans() }}</time>
            <span class="flex items-center gap-1" title="Reacciones"><x-admin.icon name="heart" class="h-4 w-4" />{{ $publication->reactions_count ?? 0 }}</span>
            <span class="flex items-center gap-1" title="Comentarios"><x-admin.icon name="chat" class="h-4 w-4" />{{ $publication->comments_count ?? 0 }}</span>
        </span>
    </div>
</article>
