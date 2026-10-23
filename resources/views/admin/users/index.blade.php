<x-layouts.admin title="Control de usuarios" description="Administra las cuentas, sus roles y su acceso al panel.">
    <x-slot name="actions">
        <x-admin.button type="button" icon="plus" x-on:click="$dispatch('open-modal', 'create-user')">Nuevo usuario</x-admin.button>
    </x-slot>

    <x-admin.modal name="create-user" title="Nuevo usuario" description="La persona podrá entrar con este correo y contraseña." icon="user"
        :show="$errors->any() && old('_form') === 'create-user'">
        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="_form" value="create-user" />
            <x-admin.field id="create-user-name" name="name" label="Nombre" :value="old('name')" required placeholder="Nombre completo" />
            <x-admin.field id="create-user-email" name="email" type="email" label="Correo" :value="old('email')" required placeholder="correo@ejemplo.com" />
            <x-admin.field id="create-user-password" name="password" type="password" label="Contraseña" required hint="Mínimo 8 caracteres." />
            <x-admin.select id="create-user-role" name="rol" label="Rol"
                :options="$roles->pluck('name', 'id')" :selected="old('rol', \App\Models\Role::USER)" />
            <p class="-mt-2 text-xs text-gray-500 dark:text-gray-400">Por defecto es <strong>Usuario</strong>: puede publicar y comentar, pero no administrar el panel.</p>
            <div class="flex flex-col-reverse gap-2 pt-2 sm:flex-row sm:justify-end">
                <x-admin.button type="button" variant="secondary" x-on:click="$dispatch('close-modal')">Cancelar</x-admin.button>
                <x-admin.button>Crear usuario</x-admin.button>
            </div>
        </form>
    </x-admin.modal>

    <x-admin.table title="Usuarios activos" :description="$users->total().' en total'">
        <x-slot name="toolbar">
            <form method="GET" class="relative w-full sm:w-72">
                <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>
                <input type="search" name="search" value="{{ $search }}" placeholder="Buscar por nombre o correo…"
                    class="block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-950 dark:text-white py-2 pl-9 text-sm shadow-sm placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 dark:focus:border-gray-400 dark:focus:ring-white/10" />
            </form>
        </x-slot>

        <x-slot name="head">
            <th>Usuario</th>
            <th>Rol</th>
            <th>Acceso</th>
            <th>Registro</th>
            <th class="text-right">Acciones</th>
        </x-slot>

        @forelse ($users as $user)
            <tr>
                <td>
                    <div class="flex items-center gap-3">
                        <x-admin.avatar :name="$user->name" />
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $user->name ?: 'Sin nombre' }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                        </div>
                    </div>
                </td>
                <td><x-admin.role-badge :role="$user->role" /></td>
                <td>
                    <div class="flex flex-wrap gap-1.5">
                        @if ($user->socialAccounts->isEmpty())
                            <x-admin.badge>Correo</x-admin.badge>
                        @endif
                        @foreach ($user->socialAccounts as $account)
                            <x-admin.badge :color="['google' => 'sky', 'microsoft' => 'emerald', 'facebook' => 'indigo'][$account->provider] ?? 'gray'">
                                {{ ucfirst($account->provider) }}
                            </x-admin.badge>
                        @endforeach
                    </div>
                </td>
                <td>
                    <p class="text-gray-900 dark:text-white">{{ $user->created_at?->locale('es')->isoFormat('D MMM YYYY') }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->created_at?->locale('es')->diffForHumans() }}</p>
                </td>
                <td>
                    <div class="flex justify-end gap-1">
                        <x-admin.icon-button icon="pencil" label="Editar" x-on:click="$dispatch('open-modal', 'edit-user-{{ $user->id }}')" />
                        @unless ($user->is(auth()->user()))
                            <x-admin.confirm-delete :action="route('admin.users.destroy', $user)" name="delete-user-{{ $user->id }}"
                                title="¿Ocultar a {{ $user->name ?: $user->email }}?"
                                message="Ya no podrá entrar al panel. Podrás restaurarlo desde Usuarios ocultos o la papelera." />
                        @endunless
                    </div>

                    <x-admin.modal name="edit-user-{{ $user->id }}" title="Editar usuario" :description="$user->email" icon="pencil">
                        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-4 whitespace-normal">
                            @csrf
                            @method('PUT')
                            <x-admin.field id="edit-user-name-{{ $user->id }}" name="name" label="Nombre" :value="$user->name" required />
                            <x-admin.field id="edit-user-email-{{ $user->id }}" name="email" type="email" label="Correo" :value="$user->email" required />
                            <x-admin.select id="edit-user-role-{{ $user->id }}" name="rol" label="Rol"
                                :options="$roles->pluck('name', 'id')" :selected="$user->rol" />
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
                <td colspan="5">
                    @if ($search !== '')
                        <x-admin.empty-state icon="users" title="Sin resultados" description="Ningún usuario coincide con “{{ $search }}”.">
                            <x-admin.button :href="route('admin.users.index')" variant="secondary" size="sm">Limpiar búsqueda</x-admin.button>
                        </x-admin.empty-state>
                    @else
                        <x-admin.empty-state icon="users" title="No hay usuarios registrados" />
                    @endif
                </td>
            </tr>
        @endforelse

        <x-slot name="footer">{{ $users->links() }}</x-slot>
    </x-admin.table>
</x-layouts.admin>
