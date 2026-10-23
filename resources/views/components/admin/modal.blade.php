{{-- Alpine modal opened with $dispatch('open-modal', name) and closed with $dispatch('close-modal'). --}}
@props(['name', 'title', 'description' => null, 'icon' => null, 'tone' => 'gray', 'show' => false])

<div
    x-data="{ show: @js($show) }"
    x-on:open-modal.window="show = $event.detail === @js($name)"
    x-on:close-modal.window="show = false"
    x-on:keydown.escape.window="show = false"
    x-show="show"
    x-cloak
    class="fixed inset-0 z-50 flex items-end justify-center overflow-y-auto whitespace-normal p-4 text-left sm:items-center"
    role="dialog"
    aria-modal="true"
>
    <div class="fixed inset-0 bg-gray-950/50 backdrop-blur-sm" x-on:click="show = false"
        x-show="show" x-transition.opacity></div>

    {{-- No overflow-hidden here: dropdowns inside the form must be able to extend past the panel. --}}
    <div class="relative w-full max-w-lg rounded-2xl bg-white shadow-2xl ring-1 ring-gray-900/5 dark:bg-gray-900 dark:ring-white/10"
        x-show="show"
        x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 sm:scale-100" x-transition:leave-end="opacity-0 sm:scale-95">
        <div class="flex items-start gap-4 px-6 pt-6">
            @if ($icon)
                <span @class([
                    'flex h-10 w-10 shrink-0 items-center justify-center rounded-full',
                    'bg-gray-100 text-gray-700 dark:bg-white/5 dark:text-gray-300' => $tone === 'gray',
                    'bg-red-100 text-red-600 dark:bg-red-500/10 dark:text-red-400' => $tone === 'red',
                    'bg-emerald-100 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400' => $tone === 'emerald',
                ])>
                    <x-admin.icon :name="$icon" />
                </span>
            @endif
            <div class="min-w-0 flex-1">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white">{{ $title }}</h2>
                @if ($description)
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $description }}</p>
                @endif
            </div>
            <button type="button" class="-mr-2 -mt-1 rounded-lg p-1.5 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-gray-800 dark:hover:text-gray-200" x-on:click="show = false">
                <x-admin.icon name="x" />
                <span class="sr-only">Cerrar</span>
            </button>
        </div>

        <div class="px-6 pb-6 pt-5">
            {{ $slot }}
        </div>
    </div>
</div>
