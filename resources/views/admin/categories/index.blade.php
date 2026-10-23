<x-layouts.admin title="Categorías">
    <div class="mb-4 flex justify-end">
        <x-button type="button" x-on:click="$dispatch('open-modal', 'create-category')">Nueva categoría</x-button>
    </div>

    <x-admin.modal name="create-category" title="Crear categoría">
        <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-4">
            @csrf
            <div>
                <x-label for="create-category-name" value="Nombre" />
                <x-input id="create-category-name" name="name" type="text" class="mt-1 block w-full" required />
            </div>
            <div class="flex justify-end gap-2">
                <x-secondary-button x-on:click="$dispatch('close-modal')">Cerrar</x-secondary-button>
                <x-button>Guardar</x-button>
            </div>
        </form>
    </x-admin.modal>

    <x-admin.table>
        <x-slot name="head">
            <th>Id</th>
            <th>Nombre</th>
            <th class="text-right">Acciones</th>
        </x-slot>

        @forelse ($categories as $category)
            <tr>
                <td>{{ $category->id }}</td>
                <td>{{ $category->name }}</td>
                <td>
                    <div class="flex justify-end gap-2">
                        <x-secondary-button x-on:click="$dispatch('open-modal', 'edit-category-{{ $category->id }}')">Editar</x-secondary-button>

                        <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('¿Eliminar esta categoría?')">
                            @csrf
                            @method('DELETE')
                            <x-danger-button type="submit">Eliminar</x-danger-button>
                        </form>
                    </div>

                    <x-admin.modal name="edit-category-{{ $category->id }}" title="Editar categoría">
                        <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="space-y-4">
                            @csrf
                            @method('PUT')
                            <p class="text-sm text-gray-600">Nombre actual: <strong>{{ $category->name }}</strong></p>
                            <div>
                                <x-label for="edit-category-name-{{ $category->id }}" value="Nuevo nombre" />
                                <x-input id="edit-category-name-{{ $category->id }}" name="name" type="text" class="mt-1 block w-full" :value="$category->name" required />
                            </div>
                            <div class="flex justify-end gap-2">
                                <x-secondary-button x-on:click="$dispatch('close-modal')">Cerrar</x-secondary-button>
                                <x-button>Guardar</x-button>
                            </div>
                        </form>
                    </x-admin.modal>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="3" class="text-center text-gray-500">No hay categorías registradas.</td>
            </tr>
        @endforelse
    </x-admin.table>

    <div class="mt-4">{{ $categories->links() }}</div>
</x-layouts.admin>
