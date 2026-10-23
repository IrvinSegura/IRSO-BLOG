<x-layouts.admin title="Usuarios ocultos">
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
                <td colspan="6" class="text-center text-gray-500">No hay usuarios ocultos.</td>
            </tr>
        @endforelse
    </x-admin.table>

    <div class="mt-4">{{ $users->links() }}</div>
</x-layouts.admin>
