{{-- Sun/moon button that switches the shared light and dark theme (see x-theme-script). --}}
<button type="button" role="switch"
    x-data="{ dark: document.documentElement.classList.contains('dark') }"
    x-on:theme-changed.window="dark = $event.detail.dark"
    x-on:click="window.irsoSetTheme(! dark)"
    x-bind:aria-checked="dark.toString()"
    x-bind:title="dark ? 'Cambiar a tema claro' : 'Cambiar a tema oscuro'"
    title="Cambiar tema"
    {{ $attributes->class('relative inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-600 shadow-sm transition hover:bg-gray-50 hover:text-gray-900 dark:border-gray-700 dark:bg-gray-900 dark:text-yellow-400 dark:hover:bg-gray-800') }}>
    <x-admin.icon name="moon" class="h-[18px] w-[18px]" x-show="! dark" />
    <x-admin.icon name="sun" class="h-[18px] w-[18px]" x-show="dark" x-cloak />
    <span class="sr-only" x-text="dark ? 'Cambiar a tema claro' : 'Cambiar a tema oscuro'">Cambiar tema</span>
</button>
