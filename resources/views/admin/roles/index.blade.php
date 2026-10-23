<x-layouts.admin title="Roles" description="Define los tipos de cuenta y cuántas personas tiene cada uno.">
    <x-slot name="actions">
        <x-admin.button type="button" icon="plus" x-on:click="$dispatch('open-modal', 'create-role')">Nuevo rol</x-admin.button>
    </x-slot>

    <x-admin.modal name="create-role" title="Nuevo rol" description="Después podrás asignarlo desde Control de usuarios." icon="shield">
        <form method="POST" action="{{ route('admin.roles.store') }}" class="space-y-4">
            @csrf
            <x-admin.field id="create-role-name" name="name" label="Nombre" required placeholder="Ej. Moderador" />
            <div class="flex flex-col-reverse gap-2 pt-2 sm:flex-row sm:justify-end">
                <x-admin.button type="button" variant="secondary" x-on:click="$dispatch('close-modal')">Cancelar</x-admin.button>
                <x-admin.button>Crear rol</x-admin.button>
            </div>
        </form>
    </x-admin.modal>

    @php($maxUsers = max(1, $roles->max('users_count')))

    <x-admin.table title="Roles activos" :description="$roles->total().' en total'">
        <x-slot name="head">
            <th>Rol</th>
            <th class="w-1/3">Usuarios</th>
            <th>Creado</th>
            <th class="text-right">Acciones</th>
        </x-slot>

        @forelse ($roles as $role)
            <tr>
                <td>
                    <div class="flex items-center gap-3">
                        <x-admin.role-badge :role="$role" />
                        @if (in_array($role->id, [\App\Models\Role::ADMIN, \App\Models\Role::EDITOR, \App\Models\Role::USER]))
                            <span class="text-xs text-gray-400">Del sistema</span>
                        @endif
                    </div>
                </td>
                <td>
                    <div class="flex items-center gap-3">
                        <div class="h-1.5 w-full max-w-40 overflow-hidden rounded-full bg-gray-100 dark:bg-gray-800">
                            <div class="h-full rounded-full bg-gray-900 dark:bg-gray-300" style="width: {{ round($role->users_count / $maxUsers * 100) }}%"></div>
                        </div>
                        <span class="w-6 text-right font-medium text-gray-900 dark:text-white">{{ $role->users_count }}</span>
                    </div>
                </td>
                <td class="text-gray-500 dark:text-gray-400">{{ $role->created_at?->locale('es')->isoFormat('D MMM YYYY') ?? '—' }}</td>
                <td>
                    <div class="flex justify-end gap-1">
                        <x-admin.icon-button icon="pencil" label="Editar" x-on:click="$dispatch('open-modal', 'edit-role-{{ $role->id }}')" />
                        <x-admin.confirm-delete :action="route('admin.roles.destroy', $role)" name="delete-role-{{ $role->id }}"
                            title="¿Eliminar el rol {{ $role->name }}?"
                            message="Se moverá a la papelera de reciclaje. Los usuarios con este rol lo conservarán hasta que se los cambies." />
                    </div>

                    <x-admin.modal name="edit-role-{{ $role->id }}" title="Editar rol" :description="'Nombre actual: '.$role->name" icon="pencil">
                        <form method="POST" action="{{ route('admin.roles.update', $role) }}" class="space-y-4 whitespace-normal">
                            @csrf
                            @method('PUT')
                            <x-admin.field id="edit-role-name-{{ $role->id }}" name="name" label="Nuevo nombre" :value="$role->name" required />
                            <div class="flex flex-col-reverse gap-2 pt-2 sm:flex-row sm:justify-end">
                                <x-admin.button type="button" variant="secondary" x-on:click="$dispatch('close-modal')">Cancelar</x-admin.button>
                                <x-admin.button>Guardar cambios</x-admin.button>
                            </div>
                        </form>
                    </x-admin.modal>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4"><x-admin.empty-state icon="shield" title="No hay roles registrados" /></td>
            </tr>
        @endforelse

        <x-slot name="footer">{{ $roles->links() }}</x-slot>
    </x-admin.table>
</x-layouts.admin>
