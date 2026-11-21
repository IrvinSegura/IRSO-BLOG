@php
    $reactionCounts = $reactions->pluck('count', 'key');
    $reactionTotal = $reactionCounts->sum();
@endphp

<x-layouts.blog :title="$publication->title" :description="\Illuminate\Support\Str::limit($publication->content, 155)">
    <article class="mx-auto max-w-3xl px-4 pb-16 pt-8 sm:px-6 lg:px-8">
        <a href="{{ url()->previous() !== url()->current() && str_starts_with(url()->previous(), route('blog.index')) ? url()->previous() : route('blog.index') }}"
            class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 transition hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
            <x-admin.icon name="arrow-left" class="h-4 w-4" /> Volver al blog
        </a>

        <header class="mt-6 animate-fade-up">
            @if ($publication->category)
                <a href="{{ route('blog.index', ['categoria' => $publication->category_id]) }}"
                    class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-bold uppercase tracking-wide text-amber-800 transition hover:bg-amber-200 dark:bg-amber-400/15 dark:text-amber-300 dark:hover:bg-amber-400/25">
                    {{ $publication->category->name }}
                </a>
            @endif
            <h1 class="mt-4 text-3xl font-extrabold leading-tight tracking-tight sm:text-4xl">{{ $publication->title }}</h1>

            <div class="mt-6 flex flex-wrap items-center gap-x-3 gap-y-2 text-sm text-gray-500 dark:text-gray-400">
                <x-admin.avatar :name="$publication->user?->name" />
                <div class="mr-2">
                    <p class="font-semibold text-gray-900 dark:text-white">{{ $publication->user?->name ?? 'Autor eliminado' }}</p>
                    <p>
                        <time datetime="{{ $publication->created_at?->toIso8601String() }}" title="{{ $publication->created_at?->locale('es')->isoFormat('LLLL') }}">
                            {{ $publication->created_at?->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}
                        </time>
                        · {{ $publication->reading_minutes }} min de lectura
                    </p>
                </div>
            </div>
        </header>

        <figure class="mt-8 animate-fade-up overflow-hidden rounded-3xl bg-gray-100 shadow-lg dark:bg-gray-800" style="--delay: 80ms">
            <img src="{{ $publication->src_img_url }}" alt="Portada de «{{ $publication->title }}»" class="aspect-[16/9] w-full object-cover" />
        </figure>

        <div class="prose prose-lg prose-gray mt-10 max-w-none animate-fade-up whitespace-pre-line dark:prose-invert" style="--delay: 140ms">{{ $publication->content }}</div>

        {{-- Actions: reactions, save, share, report --}}
        <section class="mt-10 rounded-2xl border border-gray-200/80 bg-gray-50/70 p-4 dark:border-gray-800 dark:bg-gray-900 sm:p-5" aria-label="Acciones">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div x-data="{
                        mine: @js($myReaction),
                        counts: @js($reactionCounts),
                        busy: false,
                        get total() { return Object.values(this.counts).reduce((a, b) => a + b, 0) },
                        async react(key) {
                            if (this.busy) return;
                            this.busy = true;
                            const before = { mine: this.mine, counts: { ...this.counts } };
                            if (this.mine === key) { this.counts[key]--; this.mine = null; }
                            else { if (this.mine) this.counts[this.mine]--; this.counts[key]++; this.mine = key; }
                            try {
                                const response = await fetch(@js(route('blog.reaction', $publication)), {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                                    body: JSON.stringify({ type: key }),
                                });
                                if (! response.ok) throw new Error(response.status);
                                const data = await response.json();
                                this.mine = data.mine;
                                this.counts = data.counts;
                            } catch (e) {
                                this.mine = before.mine;
                                this.counts = before.counts;
                            } finally {
                                this.busy = false;
                            }
                        },
                    }">
                    <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                        ¿Qué te pareció? <span class="font-normal normal-case" x-text="total ? '· ' + total + (total === 1 ? ' reacción' : ' reacciones') : ''">{{ $reactionTotal ? '· '.$reactionTotal.($reactionTotal === 1 ? ' reacción' : ' reacciones') : '' }}</span>
                    </p>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($reactions as $reaction)
                            <form method="POST" action="{{ route('blog.reaction', $publication) }}" x-on:submit.prevent="react(@js($reaction['key']))">
                                @csrf
                                <input type="hidden" name="type" value="{{ $reaction['key'] }}">
                                <button type="submit" title="{{ $reaction['label'] }}"
                                    x-bind:aria-pressed="mine === @js($reaction['key'])"
                                    x-bind:class="mine === @js($reaction['key'])
                                        ? 'border-amber-400 bg-amber-50 text-amber-800 ring-2 ring-amber-400/40 dark:bg-amber-400/15 dark:text-amber-200'
                                        : 'border-gray-200 bg-white text-gray-700 hover:border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:border-gray-600'"
                                    @class([
                                        'inline-flex items-center gap-1.5 rounded-full border px-3 py-1.5 text-sm font-medium shadow-sm transition focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-400',
                                        'border-amber-400 bg-amber-50 text-amber-800' => $myReaction === $reaction['key'],
                                        'border-gray-200 bg-white text-gray-700' => $myReaction !== $reaction['key'],
                                    ])>
                                    <span class="text-base leading-none" aria-hidden="true" x-bind:class="mine === @js($reaction['key']) && 'animate-pop'">{{ $reaction['emoji'] }}</span>
                                    <span class="sr-only">{{ $reaction['label'] }}</span>
                                    <span class="tabular-nums" x-text="counts[@js($reaction['key'])] || ''">{{ $reaction['count'] ?: '' }}</span>
                                </button>
                            </form>
                        @endforeach
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <form method="POST" action="{{ route('blog.save', $publication) }}"
                        x-data="{
                            saved: @js($isSaved),
                            busy: false,
                            async toggle() {
                                if (this.busy) return;
                                this.busy = true;
                                this.saved = ! this.saved;
                                try {
                                    const response = await fetch($el.action, {
                                        method: 'POST',
                                        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                                    });
                                    if (! response.ok) throw new Error(response.status);
                                    this.saved = (await response.json()).saved;
                                } catch (e) {
                                    this.saved = ! this.saved;
                                } finally {
                                    this.busy = false;
                                }
                            },
                        }"
                        x-on:submit.prevent="toggle()">
                        @csrf
                        <button type="submit" x-bind:aria-pressed="saved"
                            class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3.5 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-400 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                            <x-admin.icon name="bookmark" class="h-4 w-4 {{ $isSaved ? 'fill-current text-amber-500' : '' }}" x-bind:class="saved && 'fill-current text-amber-500'" />
                            <span x-text="saved ? 'Guardada' : 'Guardar'">{{ $isSaved ? 'Guardada' : 'Guardar' }}</span>
                        </button>
                    </form>

                    <div x-data="{ copied: false }">
                        <button type="button"
                            x-on:click="navigator.share
                                ? navigator.share({ title: @js($publication->title), url: @js($publication->url()) }).catch(() => {})
                                : navigator.clipboard.writeText(@js($publication->url())).then(() => { copied = true; setTimeout(() => copied = false, 2000) })"
                            class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3.5 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-400 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                            <x-admin.icon name="share" class="h-4 w-4" />
                            <span x-text="copied ? '¡Enlace copiado!' : 'Compartir'">Compartir</span>
                        </button>
                    </div>

                    @if ($isOwn)
                        <x-admin.button :href="route('admin.publications.edit', $publication)" variant="secondary" icon="pencil">Editar</x-admin.button>
                    @elseif ($hasReported)
                        <span class="inline-flex items-center gap-2 px-2 py-2 text-sm text-gray-500 dark:text-gray-400">
                            <x-admin.icon name="flag" class="h-4 w-4" /> Reportada
                        </span>
                    @else
                        <div x-data="{ open: @js($errors->hasAny(['reason', 'description'])) }" x-on:keydown.escape.window="open = false">
                            <button type="button" x-on:click="open = true"
                                class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-gray-500 transition hover:bg-red-50 hover:text-red-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400 dark:text-gray-400 dark:hover:bg-red-500/10 dark:hover:text-red-400">
                                <x-admin.icon name="flag" class="h-4 w-4" /> Reportar
                            </button>

                            <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-end justify-center p-4 sm:items-center" role="dialog" aria-modal="true" aria-labelledby="report-title">
                                <div x-show="open" x-transition.opacity class="absolute inset-0 bg-gray-950/60 backdrop-blur-sm" x-on:click="open = false"></div>
                                <form method="POST" action="{{ route('blog.report', $publication) }}" x-show="open" x-trap.noscroll="open"
                                    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                    class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl dark:bg-gray-900 dark:ring-1 dark:ring-white/10">
                                    @csrf
                                    <div class="flex items-start gap-3">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-400">
                                            <x-admin.icon name="flag" class="h-5 w-5" />
                                        </div>
                                        <div>
                                            <h2 id="report-title" class="text-lg font-semibold">Reportar publicación</h2>
                                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">El autor no verá quién la reportó. ¿Qué está pasando?</p>
                                        </div>
                                    </div>

                                    <fieldset class="mt-5 space-y-2">
                                        <legend class="sr-only">Motivo</legend>
                                        @foreach ($reportReasons as $value => $label)
                                            <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-gray-200 px-4 py-3 text-sm transition has-[:checked]:border-red-400 has-[:checked]:bg-red-50/60 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800 dark:has-[:checked]:bg-red-500/10">
                                                <input type="radio" name="reason" value="{{ $value }}" @checked(old('reason') === $value) required class="text-red-600 focus:ring-red-500 dark:bg-gray-800" />
                                                {{ $label }}
                                            </label>
                                        @endforeach
                                    </fieldset>
                                    @error('reason')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror

                                    <label for="report-description" class="mt-4 block text-sm font-medium">Detalles <span class="font-normal text-gray-500">(opcional)</span></label>
                                    <textarea id="report-description" name="description" rows="3" maxlength="255"
                                        class="mt-1.5 w-full rounded-xl border-gray-300 text-sm focus:border-red-400 focus:ring-red-400 dark:border-gray-700 dark:bg-gray-800">{{ old('description') }}</textarea>
                                    @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror

                                    <div class="mt-6 flex justify-end gap-2">
                                        <x-admin.button type="button" variant="secondary" x-on:click="open = false">Cancelar</x-admin.button>
                                        <x-admin.button type="submit" variant="danger">Enviar reporte</x-admin.button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </section>

        {{-- Comments --}}
        <section id="comentarios" class="mt-14 scroll-mt-20">
            <h2 class="flex items-center gap-2 text-xl font-bold tracking-tight">
                Comentarios
                <span class="rounded-full bg-gray-100 px-2.5 py-0.5 text-sm font-semibold text-gray-600 dark:bg-gray-800 dark:text-gray-300">{{ $publication->comments->count() }}</span>
            </h2>

            <form method="POST" action="{{ route('admin.publications.comments.store', $publication) }}" class="mt-6 flex gap-3"
                x-data="{ text: @js(old('comment', '')) }">
                @csrf
                <x-admin.avatar :name="auth()->user()->name" class="mt-1 hidden sm:flex" />
                <div class="flex-1">
                    <label for="comment" class="sr-only">Escribe un comentario</label>
                    <textarea id="comment" name="comment" rows="3" maxlength="255" required x-model="text"
                        placeholder="Comparte lo que piensas…"
                        class="w-full rounded-2xl border-gray-300 text-sm shadow-sm focus:border-amber-400 focus:ring-amber-400 dark:border-gray-700 dark:bg-gray-900"></textarea>
                    @error('comment')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    <div class="mt-2 flex items-center justify-between">
                        <span class="text-xs text-gray-400" x-text="text.length + '/255'">0/255</span>
                        <x-admin.button type="submit" size="sm" icon="send" x-bind:disabled="! text.trim()">Comentar</x-admin.button>
                    </div>
                </div>
            </form>

            @if ($publication->comments->isEmpty())
                <x-admin.empty-state icon="chat" title="Aún no hay comentarios" description="Sé la primera persona en comentar." class="mt-6 rounded-2xl bg-gray-50 py-10 dark:bg-gray-900" />
            @else
                <ol class="mt-8 space-y-6">
                    @foreach ($publication->comments as $comment)
                        <li class="flex animate-fade-up gap-3" style="--delay: {{ min($loop->index, 8) * 40 }}ms">
                            <x-admin.avatar :name="$comment->user?->name" size="sm" class="mt-0.5" />
                            <div class="min-w-0 flex-1 rounded-2xl bg-gray-50 px-4 py-3 dark:bg-gray-900">
                                <p class="flex flex-wrap items-center gap-x-2 text-sm">
                                    <span class="font-semibold">{{ $comment->user?->name ?? 'Usuario eliminado' }}</span>
                                    @if ($comment->user_id === $publication->user_id)
                                        <x-admin.badge color="yellow">Autor</x-admin.badge>
                                    @endif
                                    <time class="text-xs text-gray-500 dark:text-gray-400" datetime="{{ $comment->created_at?->toIso8601String() }}">{{ $comment->created_at?->locale('es')->diffForHumans() }}</time>
                                </p>
                                <p class="mt-1 whitespace-pre-line break-words text-sm text-gray-700 dark:text-gray-300">{{ $comment->comment }}</p>
                            </div>
                        </li>
                    @endforeach
                </ol>
            @endif
        </section>
    </article>

    @if ($related->isNotEmpty())
        <section class="border-t border-gray-200 bg-gray-50/70 py-14 dark:border-gray-800 dark:bg-gray-900/40" aria-labelledby="related-title">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <h2 id="related-title" class="text-xl font-bold tracking-tight">Sigue leyendo en {{ $publication->category?->name }}</h2>
                <div class="mt-8 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($related as $item)
                        <x-blog.card :publication="$item" :index="$loop->index" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</x-layouts.blog>
