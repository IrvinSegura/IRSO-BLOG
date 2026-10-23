<x-layouts.admin title="Papelera de reciclaje">
    <div class="space-y-8">
        <section>
            <h2 class="mb-3 text-lg font-semibold text-gray-800">Usuarios</h2>
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
                        <td class="text-right">
                            <form method="POST" action="{{ route('admin.users.restore', $user) }}">
                                @csrf
                                @method('PATCH')
                                <x-button>Restaurar</x-button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-gray-500">No hay usuarios eliminados.</td>
                    </tr>
                @endforelse
            </x-admin.table>
        </section>

        <section>
            <h2 class="mb-3 text-lg font-semibold text-gray-800">Categorías</h2>
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
                        <td class="text-right">
                            <form method="POST" action="{{ route('admin.categories.restore', $category) }}">
                                @csrf
                                @method('PATCH')
                                <x-button>Restaurar</x-button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center text-gray-500">No hay categorías eliminadas.</td>
                    </tr>
                @endforelse
            </x-admin.table>
        </section>

        <section>
            <h2 class="mb-3 text-lg font-semibold text-gray-800">Roles</h2>
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
                        <td class="text-right">
                            <form method="POST" action="{{ route('admin.roles.restore', $role) }}">
                                @csrf
                                @method('PATCH')
                                <x-button>Restaurar</x-button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center text-gray-500">No hay roles eliminados.</td>
                    </tr>
                @endforelse
            </x-admin.table>
        </section>
    </div>
</x-layouts.admin>
