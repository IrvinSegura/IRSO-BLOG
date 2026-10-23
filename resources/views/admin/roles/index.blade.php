<x-layouts.admin title="Roles">
    <div class="mb-4 flex justify-end">
        <x-button type="button" x-on:click="$dispatch('open-modal', 'create-role')">Agregar rol</x-button>
    </div>

    <x-admin.modal name="create-role" title="Nuevo rol">
        <form method="POST" action="{{ route('admin.roles.store') }}" class="space-y-4">
            @csrf
            <div>
                <x-label for="create-role-name" value="Nombre" />
                <x-input id="create-role-name" name="name" type="text" class="mt-1 block w-full" required />
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

        @forelse ($roles as $role)
            <tr>
                <td>{{ $role->id }}</td>
                <td>{{ $role->name }}</td>
                <td>
                    <div class="flex justify-end gap-2">
                        <x-secondary-button x-on:click="$dispatch('open-modal', 'edit-role-{{ $role->id }}')">Editar</x-secondary-button>

                        <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" onsubmit="return confirm('¿Eliminar este rol?')">
                            @csrf
                            @method('DELETE')
                            <x-danger-button type="submit">Eliminar</x-danger-button>
                        </form>
                    </div>

                    <x-admin.modal name="edit-role-{{ $role->id }}" title="Editar rol">
                        <form method="POST" action="{{ route('admin.roles.update', $role) }}" class="space-y-4">
                            @csrf
                            @method('PUT')
                            <p class="text-sm text-gray-600">Nombre actual: <strong>{{ $role->name }}</strong></p>
                            <div>
                                <x-label for="edit-role-name-{{ $role->id }}" value="Nuevo nombre" />
                                <x-input id="edit-role-name-{{ $role->id }}" name="name" type="text" class="mt-1 block w-full" :value="$role->name" required />
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
                <td colspan="3" class="text-center text-gray-500">No hay roles registrados.</td>
            </tr>
        @endforelse
    </x-admin.table>

    <div class="mt-4">{{ $roles->links() }}</div>
</x-layouts.admin>
