<x-layouts.admin title="Usuarios ocultos" description="Cuentas desactivadas que ya no pueden entrar. Restáuralas cuando quieras.">
    <x-admin.table title="Usuarios ocultos" :description="$users->total().' en total'">
        <x-slot name="head">
            <th>Usuario</th>
            <th>Rol</th>
            <th>Oculto desde</th>
            <th class="text-right">Acciones</th>
        </x-slot>

        @forelse ($users as $user)
            <tr>
                <td>
                    <div class="flex items-center gap-3">
                        <x-admin.avatar :name="$user->name" class="opacity-60 grayscale" />
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $user->name ?: 'Sin nombre' }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                        </div>
                    </div>
                </td>
                <td><x-admin.role-badge :role="$user->role" /></td>
                <td>
                    <p class="text-gray-900 dark:text-white">{{ $user->deleted_at?->locale('es')->isoFormat('D MMM YYYY') }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->deleted_at?->locale('es')->diffForHumans() }}</p>
                </td>
                <td class="text-right">
                    <form method="POST" action="{{ route('admin.users.restore', $user) }}">
                        @csrf
                        @method('PATCH')
                        <x-admin.button variant="secondary" size="sm" icon="restore">Restaurar</x-admin.button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4">
                    <x-admin.empty-state icon="eye-slash" title="No hay usuarios ocultos" description="Cuando ocultes a alguien desde Control de usuarios, aparecerá aquí." />
                </td>
            </tr>
        @endforelse

        <x-slot name="footer">{{ $users->links() }}</x-slot>
    </x-admin.table>
</x-layouts.admin>
