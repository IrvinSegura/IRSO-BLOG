@php
    $tabs = [
        'users' => ['label' => 'Usuarios', 'icon' => 'users', 'count' => $users->count()],
        'categories' => ['label' => 'Categorías', 'icon' => 'tag', 'count' => $categories->count()],
        'roles' => ['label' => 'Roles', 'icon' => 'shield', 'count' => $roles->count()],
    ];
@endphp

<x-layouts.admin title="Papelera de reciclaje" description="Lo que eliminaste sigue aquí. Restáuralo cuando lo necesites.">
    <div x-data="{ tab: 'users' }">
        <div class="mb-6 inline-flex flex-wrap gap-1 rounded-xl bg-gray-100 dark:bg-gray-800 p-1">
            @foreach ($tabs as $key => $tab)
                <button type="button" x-on:click="tab = '{{ $key }}'"
                    class="flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition"
                    x-bind:class="tab === '{{ $key }}' ? 'bg-white text-gray-900 shadow-sm dark:bg-gray-700 dark:text-white' : 'text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white'">
                    <x-admin.icon :name="$tab['icon']" class="h-4 w-4" />
                    {{ $tab['label'] }}
                    <span class="rounded-full px-2 py-0.5 text-xs"
                        x-bind:class="tab === '{{ $key }}' ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-900' : 'bg-gray-200 text-gray-600 dark:bg-gray-700 dark:text-gray-300'">{{ $tab['count'] }}</span>
                </button>
            @endforeach
        </div>

        <div x-show="tab === 'users'">
            <x-admin.table>
                <x-slot name="head">
                    <th>Usuario</th>
                    <th>Rol</th>
                    <th>Eliminado</th>
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
                        <td class="text-gray-500 dark:text-gray-400">{{ $user->deleted_at?->locale('es')->diffForHumans() }}</td>
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
                        <td colspan="4"><x-admin.empty-state icon="users" title="No hay usuarios eliminados" /></td>
                    </tr>
                @endforelse
            </x-admin.table>
        </div>

        <div x-show="tab === 'categories'" x-cloak>
            <x-admin.table>
                <x-slot name="head">
                    <th>Categoría</th>
                    <th>Eliminada</th>
                    <th class="text-right">Acciones</th>
                </x-slot>

                @forelse ($categories as $category)
                    <tr>
                        <td class="font-medium text-gray-900 dark:text-white">{{ $category->name }}</td>
                        <td class="text-gray-500 dark:text-gray-400">{{ $category->deleted_at?->locale('es')->diffForHumans() }}</td>
                        <td class="text-right">
                            <form method="POST" action="{{ route('admin.categories.restore', $category) }}">
                                @csrf
                                @method('PATCH')
                                <x-admin.button variant="secondary" size="sm" icon="restore">Restaurar</x-admin.button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3"><x-admin.empty-state icon="tag" title="No hay categorías eliminadas" /></td>
                    </tr>
                @endforelse
            </x-admin.table>
        </div>

        <div x-show="tab === 'roles'" x-cloak>
            <x-admin.table>
                <x-slot name="head">
                    <th>Rol</th>
                    <th>Eliminado</th>
                    <th class="text-right">Acciones</th>
                </x-slot>

                @forelse ($roles as $role)
                    <tr>
                        <td><x-admin.role-badge :role="$role" /></td>
                        <td class="text-gray-500 dark:text-gray-400">{{ $role->deleted_at?->locale('es')->diffForHumans() }}</td>
                        <td class="text-right">
                            <form method="POST" action="{{ route('admin.roles.restore', $role) }}">
                                @csrf
                                @method('PATCH')
                                <x-admin.button variant="secondary" size="sm" icon="restore">Restaurar</x-admin.button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3"><x-admin.empty-state icon="shield" title="No hay roles eliminados" /></td>
                    </tr>
                @endforelse
            </x-admin.table>
        </div>
    </div>
</x-layouts.admin>
