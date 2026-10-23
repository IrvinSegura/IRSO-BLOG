<x-layouts.admin title="Estadísticas">
    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-lg bg-white p-6 shadow">
            <h2 class="mb-4 text-sm font-semibold text-gray-700">Usuarios por rol</h2>
            <div class="relative h-72">
                <canvas
                    id="users-per-role-bar"
                    data-labels='@json($roles->pluck('name'))'
                    data-values='@json($roles->pluck('users_count'))'
                ></canvas>
            </div>
        </div>

        <div class="rounded-lg bg-white p-6 shadow">
            <h2 class="mb-4 text-sm font-semibold text-gray-700">Distribución de usuarios</h2>
            <div class="relative h-72">
                <canvas
                    id="users-per-role-doughnut"
                    data-labels='@json($roles->pluck('name'))'
                    data-values='@json($roles->pluck('users_count'))'
                ></canvas>
            </div>
        </div>
    </div>

    @push('scripts')
        @vite('resources/js/statistics.js')
    @endpush
</x-layouts.admin>
