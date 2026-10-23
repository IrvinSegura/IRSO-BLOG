{{-- Alpine modal opened with $dispatch('open-modal', name) and closed with $dispatch('close-modal'). --}}
@props(['name', 'title'])

<div
    x-data="{ show: false }"
    x-on:open-modal.window="show = $event.detail === @js($name)"
    x-on:close-modal.window="show = false"
    x-on:keydown.escape.window="show = false"
    x-show="show"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto px-4 py-6 text-left"
    role="dialog"
    aria-modal="true"
>
    <div class="fixed inset-0 bg-gray-500/75" x-on:click="show = false"></div>

    <div class="relative w-full max-w-lg rounded-lg bg-white shadow-xl" x-show="show" x-transition>
        <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
            <h2 class="text-lg font-medium text-gray-900">{{ $title }}</h2>
            <button type="button" class="text-2xl leading-none text-gray-400 hover:text-gray-600" x-on:click="show = false" aria-label="Cerrar">&times;</button>
        </div>

        <div class="px-6 py-4">
            {{ $slot }}
        </div>
    </div>
</div>
