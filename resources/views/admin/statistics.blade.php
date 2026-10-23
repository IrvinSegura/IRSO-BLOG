<x-layouts.admin title="Estadísticas" description="Cómo crece tu comunidad y tu contenido.">
    <section class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
        <x-admin.stat-card label="Usuarios activos" :value="number_format($totals['users'])" icon="users" color="indigo" />
        <x-admin.stat-card label="Publicaciones" :value="number_format($totals['publications'])" icon="document" color="yellow" />
        <x-admin.stat-card label="Comentarios" :value="number_format($totals['comments'])" icon="chat" color="emerald" />
        <x-admin.stat-card label="Comentarios por publicación" :value="$totals['commentsPerPublication']" icon="sparkles" color="rose" />
    </section>

    <div class="mt-8 grid gap-6 lg:grid-cols-5">
        <section class="rounded-2xl border border-gray-200/80 bg-white dark:border-gray-800 dark:bg-gray-900 p-6 shadow-sm lg:col-span-3">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Publicaciones por mes</h2>
            <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">Últimos 6 meses</p>
            <div class="relative mt-6 h-72">
                <canvas id="publications-per-month"
                    data-labels='@json($publicationsPerMonth['labels'])'
                    data-values='@json($publicationsPerMonth['values'])'></canvas>
            </div>
        </section>

        <section class="rounded-2xl border border-gray-200/80 bg-white dark:border-gray-800 dark:bg-gray-900 p-6 shadow-sm lg:col-span-2">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Distribución de usuarios</h2>
            <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">Por rol asignado</p>
            <div class="relative mt-6 h-72">
                <canvas id="users-per-role-doughnut"
                    data-labels='@json($roles->pluck('name'))'
                    data-values='@json($roles->pluck('users_count'))'></canvas>
            </div>
        </section>

        <section class="rounded-2xl border border-gray-200/80 bg-white dark:border-gray-800 dark:bg-gray-900 p-6 shadow-sm lg:col-span-2">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Usuarios por rol</h2>
            <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">Cantidad de cuentas activas</p>
            <div class="relative mt-6 h-72">
                <canvas id="users-per-role-bar"
                    data-labels='@json($roles->pluck('name'))'
                    data-values='@json($roles->pluck('users_count'))'></canvas>
            </div>
        </section>

        <section class="rounded-2xl border border-gray-200/80 bg-white dark:border-gray-800 dark:bg-gray-900 p-6 shadow-sm lg:col-span-3">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Categorías con más publicaciones</h2>
            <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">Las 8 principales</p>
            <div class="relative mt-6 h-72">
                <canvas id="publications-per-category"
                    data-labels='@json($categories->pluck('name'))'
                    data-values='@json($categories->pluck('publications_count'))'></canvas>
            </div>
        </section>
    </div>

    @push('scripts')
        @vite('resources/js/statistics.js')
    @endpush
</x-layouts.admin>
