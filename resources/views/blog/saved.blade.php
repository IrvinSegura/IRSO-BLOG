<x-layouts.blog title="Guardados">
    <section class="mx-auto max-w-7xl px-4 pb-20 pt-10 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-end justify-between gap-3 border-b border-gray-200 pb-5 dark:border-gray-800">
            <div>
                <p class="inline-flex items-center gap-1.5 text-xs font-semibold uppercase tracking-widest text-amber-600 dark:text-amber-400">
                    <x-admin.icon name="bookmark" class="h-4 w-4" /> Tu lista
                </p>
                <h1 class="mt-2 text-3xl font-extrabold tracking-tight">Guardados</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Lo que apartaste para leer con calma.</p>
            </div>
            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $saves->total() }} {{ $saves->total() === 1 ? 'publicación' : 'publicaciones' }}</span>
        </div>

        @if ($saves->isEmpty())
            <x-admin.empty-state icon="bookmark" title="Aún no guardas nada"
                description="Abre cualquier publicación y toca «Guardar» para encontrarla aquí después."
                class="mt-8 rounded-3xl bg-gray-50 dark:bg-gray-900">
                <x-admin.button :href="route('blog.index')" icon="arrow-right" size="sm">Explorar el blog</x-admin.button>
            </x-admin.empty-state>
        @else
            <div class="mt-8 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($saves as $save)
                    <div class="relative">
                        <x-blog.card :publication="$save->publication" :index="$loop->index" />
                        <form method="POST" action="{{ route('blog.save', $save->publication) }}" class="absolute right-3 top-3 z-10">
                            @csrf
                            <button type="submit" title="Quitar de guardados"
                                class="flex h-9 w-9 items-center justify-center rounded-full bg-white/95 text-amber-600 shadow-sm transition hover:bg-white hover:text-red-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-400">
                                <x-admin.icon name="bookmark" class="h-4 w-4 fill-current" />
                                <span class="sr-only">Quitar «{{ $save->publication->title }}» de guardados</span>
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>

            @if ($saves->hasPages())
                <nav class="mt-10" aria-label="Paginación">{{ $saves->links() }}</nav>
            @endif
        @endif
    </section>
</x-layouts.blog>
