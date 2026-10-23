<x-layouts.admin title="Categorías" :description="$categories->total().' categorías para organizar tus publicaciones'">
    <x-slot name="actions">
        <x-admin.button type="button" icon="plus" x-on:click="$dispatch('open-modal', 'create-category')">Nueva categoría</x-admin.button>
    </x-slot>

    <x-admin.modal name="create-category" title="Nueva categoría" description="Agrupa publicaciones con un tema en común." icon="tag">
        <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-4">
            @csrf
            <x-admin.field id="create-category-name" name="name" label="Nombre" required placeholder="Ej. Tecnología" />
            <div class="flex flex-col-reverse gap-2 pt-2 sm:flex-row sm:justify-end">
                <x-admin.button type="button" variant="secondary" x-on:click="$dispatch('close-modal')">Cancelar</x-admin.button>
                <x-admin.button>Crear categoría</x-admin.button>
            </div>
        </form>
    </x-admin.modal>

    @php($accents = ['from-amber-400 to-orange-500', 'from-indigo-400 to-violet-500', 'from-emerald-400 to-teal-500', 'from-rose-400 to-pink-500', 'from-sky-400 to-blue-500', 'from-fuchsia-400 to-purple-500'])

    @if ($categories->isEmpty())
        <div class="rounded-2xl border border-dashed border-gray-300 bg-white dark:border-gray-700 dark:bg-gray-900">
            <x-admin.empty-state icon="tag" title="No hay categorías" description="Crea la primera para empezar a organizar tu contenido.">
                <x-admin.button type="button" icon="plus" size="sm" x-on:click="$dispatch('open-modal', 'create-category')">Nueva categoría</x-admin.button>
            </x-admin.empty-state>
        </div>
    @else
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($categories as $category)
                <div class="group relative flex items-center gap-4 rounded-2xl border border-gray-200/80 bg-white dark:border-gray-800 dark:bg-gray-900 p-4 shadow-sm transition hover:shadow-md">
                    <span @class(['flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br text-lg font-bold text-white shadow-sm', $accents[$category->id % count($accents)]])>
                        {{ mb_strtoupper(mb_substr($category->name, 0, 1)) }}
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="truncate font-semibold text-gray-900 dark:text-white">{{ $category->name }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $category->publications_count }} {{ $category->publications_count === 1 ? 'publicación' : 'publicaciones' }}
                        </p>
                    </div>
                    <div class="flex gap-1 opacity-100 transition sm:opacity-0 sm:group-hover:opacity-100 sm:group-focus-within:opacity-100">
                        <x-admin.icon-button icon="pencil" label="Editar" x-on:click="$dispatch('open-modal', 'edit-category-{{ $category->id }}')" />
                        <x-admin.confirm-delete :action="route('admin.categories.destroy', $category)" name="delete-category-{{ $category->id }}"
                            title="¿Eliminar la categoría {{ $category->name }}?"
                            message="Se moverá a la papelera de reciclaje. Sus publicaciones no se borran." />
                    </div>

                    <x-admin.modal name="edit-category-{{ $category->id }}" title="Editar categoría" :description="'Nombre actual: '.$category->name" icon="pencil">
                        <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="space-y-4">
                            @csrf
                            @method('PUT')
                            <x-admin.field id="edit-category-name-{{ $category->id }}" name="name" label="Nuevo nombre" :value="$category->name" required />
                            <div class="flex flex-col-reverse gap-2 pt-2 sm:flex-row sm:justify-end">
                                <x-admin.button type="button" variant="secondary" x-on:click="$dispatch('close-modal')">Cancelar</x-admin.button>
                                <x-admin.button>Guardar cambios</x-admin.button>
                            </div>
                        </form>
                    </x-admin.modal>
                </div>
            @endforeach
        </div>

        <div class="mt-8">{{ $categories->links() }}</div>
    @endif
</x-layouts.admin>
