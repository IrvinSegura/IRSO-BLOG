<x-layouts.admin title="Publicaciones">
    <div class="mb-4 flex flex-wrap justify-end gap-3">
        <x-link-button href="{{ route('admin.publications.export') }}" secondary>Exportar JSON</x-link-button>
        <x-link-button href="{{ route('admin.publications.create') }}">Crear publicación</x-link-button>
    </div>

    <div class="space-y-6">
        @forelse ($publications as $publication)
            <article class="overflow-hidden rounded-lg bg-white shadow" x-data="{ showComments: false }">
                <div class="flex flex-col gap-6 p-6 md:flex-row">
                    <img src="{{ $publication->src_img_url }}" alt="{{ $publication->title }}" class="h-56 w-full rounded-md object-cover md:w-96" />

                    <div class="flex-1">
                        <span class="inline-block rounded-full bg-yellow-100 px-3 py-1 text-xs font-semibold text-yellow-800">
                            {{ $publication->category?->name }}
                        </span>
                        <h2 class="mt-2 text-2xl font-bold text-gray-900">{{ $publication->title }}</h2>
                        <p class="mt-3 text-justify text-gray-700">{{ $publication->content }}</p>

                        <dl class="mt-4 grid grid-cols-2 gap-x-4 gap-y-1 text-sm">
                            <dt class="font-semibold text-gray-600">Autor</dt>
                            <dd>{{ $publication->user?->name }}</dd>
                            <dt class="font-semibold text-gray-600">Publicado</dt>
                            <dd>{{ $publication->created_at?->locale('es')->diffForHumans() }}</dd>
                            <dt class="font-semibold text-gray-600">Empresa</dt>
                            <dd>IRSO BLOG</dd>
                            <dt class="font-semibold text-gray-600">Id publicación</dt>
                            <dd>{{ $publication->id }}</dd>
                        </dl>
                    </div>
                </div>

                <div class="border-t border-gray-200 bg-gray-50 p-6">
                    <form method="POST" action="{{ route('admin.publications.comments.store', $publication) }}" class="flex gap-2">
                        @csrf
                        <x-input name="comment" type="text" class="block w-full" placeholder="Escribe un comentario" required />
                        <x-button>Comentar</x-button>
                    </form>

                    <button type="button" class="mt-4 text-sm font-medium text-indigo-600 hover:underline" x-on:click="showComments = ! showComments">
                        <span x-text="showComments ? 'Ocultar comentarios' : 'Ver comentarios ({{ $publication->comments->count() }})'"></span>
                    </button>

                    <ul class="mt-4 space-y-3" x-show="showComments" x-cloak>
                        @forelse ($publication->comments as $comment)
                            <li class="rounded-md bg-white p-4 shadow-sm">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="font-semibold text-gray-800">{{ $comment->user?->name }}</span>
                                    <span class="text-gray-500">{{ $comment->created_at?->locale('es')->diffForHumans() }}</span>
                                </div>
                                <p class="mt-1 text-gray-700">{{ $comment->comment }}</p>
                            </li>
                        @empty
                            <li class="text-sm text-gray-500">Aún no hay comentarios.</li>
                        @endforelse
                    </ul>
                </div>
            </article>
        @empty
            <p class="rounded-lg bg-white p-6 text-center text-gray-500 shadow">Aún no hay publicaciones.</p>
        @endforelse
    </div>

    <div class="mt-6">{{ $publications->links() }}</div>
</x-layouts.admin>
