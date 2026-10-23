<x-layouts.admin title="Control de usuarios">
    <div class="mb-4 flex justify-end">
        <x-button type="button" x-on:click="$dispatch('open-modal', 'create-user')">Nuevo usuario</x-button>
    </div>

    <x-admin.modal name="create-user" title="Nuevo usuario">
        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
            @csrf
            <div>
                <x-label for="create-user-name" value="Nombre" />
                <x-input id="create-user-name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required />
            </div>
            <div>
                <x-label for="create-user-email" value="Correo" />
                <x-input id="create-user-email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" required />
            </div>
            <div>
                <x-label for="create-user-password" value="Contraseña" />
                <x-input id="create-user-password" name="password" type="password" class="mt-1 block w-full" required />
            </div>
            <div>
                <x-label for="create-user-role" value="Rol" />
                <x-select id="create-user-role" name="rol" class="mt-1 block w-full">
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                    @endforeach
                </x-select>
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
            <th>Correo</th>
            <th>Rol</th>
            <th>Creación</th>
            <th class="text-right">Acciones</th>
        </x-slot>

        @forelse ($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->role?->name }}</td>
                <td>{{ $user->created_at }}</td>
                <td>
                    <div class="flex justify-end gap-2">
                        <x-secondary-button x-on:click="$dispatch('open-modal', 'edit-user-{{ $user->id }}')">Editar</x-secondary-button>

                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('¿Eliminar este usuario?')">
                            @csrf
                            @method('DELETE')
                            <x-danger-button type="submit">Eliminar</x-danger-button>
                        </form>
                    </div>

                    <x-admin.modal name="edit-user-{{ $user->id }}" title="Editar usuario">
                        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-4">
                            @csrf
                            @method('PUT')
                            <div>
                                <x-label for="edit-user-name-{{ $user->id }}" value="Nombre" />
                                <x-input id="edit-user-name-{{ $user->id }}" name="name" type="text" class="mt-1 block w-full" :value="$user->name" required />
                            </div>
                            <div>
                                <x-label for="edit-user-email-{{ $user->id }}" value="Correo" />
                                <x-input id="edit-user-email-{{ $user->id }}" name="email" type="email" class="mt-1 block w-full" :value="$user->email" required />
                            </div>
                            <div>
                                <x-label for="edit-user-role-{{ $user->id }}" value="Rol" />
                                <x-select id="edit-user-role-{{ $user->id }}" name="rol" class="mt-1 block w-full">
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}" @selected($role->id == $user->rol)>{{ $role->name }}</option>
                                    @endforeach
                                </x-select>
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
                <td colspan="6" class="text-center text-gray-500">No hay usuarios registrados.</td>
            </tr>
        @endforelse
    </x-admin.table>

    <div class="mt-4">{{ $users->links() }}</div>
</x-layouts.admin>
