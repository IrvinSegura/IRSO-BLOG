{{--
    Styled replacement for <select>: the value travels in a hidden input, the list can be searched
    when it is long, and it works with the keyboard (arrows, Enter, Escape). Dispatches
    "option-selected" with {name, value, label} so other widgets (like a preview) can react.
--}}
@props(['name', 'label', 'options' => [], 'selected' => null, 'id' => null, 'placeholder' => 'Selecciona una opción'])

@php
    $id ??= $name;
    $items = collect($options)->map(fn ($text, $value) => ['value' => (string) $value, 'label' => (string) $text])->values();
    $searchable = $items->count() > 7;
@endphp

<div
    x-data="{
        open: false,
        search: '',
        active: -1,
        value: @js($selected === null ? '' : (string) $selected),
        options: @js($items),
        normalize(text) {
            return text.toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '');
        },
        get filtered() {
            const query = this.normalize(this.search);
            return this.options.filter((option) => this.normalize(option.label).includes(query));
        },
        get current() {
            return this.options.find((option) => option.value === this.value);
        },
        show() {
            this.open = true;
            this.search = '';
            this.active = Math.max(0, this.filtered.findIndex((option) => option.value === this.value));
            this.$nextTick(() => (this.$refs.search ?? this.$refs.list).focus());
        },
        hide() {
            this.open = false;
            this.$refs.button.focus();
        },
        choose(option) {
            this.value = option.value;
            this.$dispatch('option-selected', { name: @js($name), value: option.value, label: option.label });
            this.hide();
        },
        move(step) {
            if (! this.filtered.length) return;
            this.active = (this.active + step + this.filtered.length) % this.filtered.length;
            this.$nextTick(() => this.$refs.list.querySelector('[data-active]')?.scrollIntoView({ block: 'nearest' }));
        },
    }"
    x-init="if (current) $dispatch('option-selected', { name: @js($name), value: current.value, label: current.label })"
    x-on:click.outside="open = false"
    x-on:keydown.escape="if (open) { $event.preventDefault(); $event.stopPropagation(); hide() }"
    class="relative"
>
    <label for="{{ $id }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ $label }}</label>
    <input type="hidden" name="{{ $name }}" x-bind:value="value" />

    <button type="button" id="{{ $id }}" x-ref="button"
        x-on:click="open ? (open = false) : show()"
        x-on:keydown.arrow-down.prevent="show()"
        x-on:keydown.arrow-up.prevent="show()"
        aria-haspopup="listbox" x-bind:aria-expanded="open"
        @class([
            'relative mt-1.5 flex w-full items-center justify-between gap-3 rounded-lg border bg-white py-2.5 pl-3 pr-3 text-left text-sm shadow-sm transition focus:outline-none focus:ring-2 dark:bg-gray-950',
            'border-red-300 focus:border-red-500 focus:ring-red-500/20 dark:border-red-500/50' => $errors->has($name),
            'border-gray-300 focus:border-gray-900 focus:ring-gray-900/10 dark:border-gray-700 dark:focus:border-gray-400 dark:focus:ring-white/10' => ! $errors->has($name),
        ])
        x-bind:class="open && 'border-gray-900 ring-2 ring-gray-900/10 dark:border-gray-400 dark:ring-white/10'">
        <span x-text="current?.label ?? @js($placeholder)" x-bind:class="current ? 'text-gray-900 dark:text-white' : 'text-gray-400 dark:text-gray-500'" class="truncate"></span>
        <x-admin.icon name="chevron-down" class="h-4 w-4 shrink-0 text-gray-400 transition" x-bind:class="open && 'rotate-180'" />
    </button>

    <div x-show="open" x-cloak
        x-transition:enter="ease-out duration-100" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="ease-in duration-75" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="absolute z-30 mt-2 w-full overflow-hidden rounded-xl bg-white shadow-lg ring-1 ring-gray-900/10 dark:bg-gray-900 dark:ring-white/10">
        @if ($searchable)
            <div class="border-b border-gray-100 p-2 dark:border-gray-800">
                <div class="relative">
                    <svg class="pointer-events-none absolute left-2.5 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                    <input type="text" x-ref="search" x-model="search" placeholder="Buscar…"
                        x-on:input="active = 0"
                        x-on:keydown.arrow-down.prevent="move(1)"
                        x-on:keydown.arrow-up.prevent="move(-1)"
                        x-on:keydown.enter.prevent="filtered[active] && choose(filtered[active])"
                        class="block w-full rounded-lg border-0 bg-gray-50 py-2 pl-8 text-sm placeholder:text-gray-400 focus:ring-2 focus:ring-gray-900/10 dark:bg-gray-800 dark:text-white dark:placeholder:text-gray-500 dark:focus:ring-white/10" />
                </div>
            </div>
        @endif

        <ul x-ref="list" tabindex="-1" role="listbox" class="max-h-60 overflow-y-auto p-1.5 focus:outline-none"
            x-on:keydown.arrow-down.prevent="move(1)"
            x-on:keydown.arrow-up.prevent="move(-1)"
            x-on:keydown.enter.prevent="filtered[active] && choose(filtered[active])">
            <template x-for="(option, index) in filtered" :key="option.value">
                <li role="option" x-bind:aria-selected="option.value === value"
                    x-on:click="choose(option)" x-on:mousemove="active = index"
                    x-bind:data-active="index === active ? true : null"
                    x-bind:class="index === active ? 'bg-gray-100 text-gray-900 dark:bg-gray-800 dark:text-white' : 'text-gray-700 dark:text-gray-300'"
                    class="flex cursor-pointer items-center justify-between gap-3 rounded-lg px-3 py-2 text-sm">
                    <span x-text="option.label" x-bind:class="option.value === value && 'font-semibold'" class="truncate"></span>
                    <svg x-show="option.value === value" class="h-4 w-4 shrink-0 text-gray-900 dark:text-yellow-400" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                </li>
            </template>
            <li x-show="! filtered.length" class="px-3 py-6 text-center text-sm text-gray-500 dark:text-gray-400">Sin resultados</li>
        </ul>
    </div>

    @error($name)
        <p class="mt-1.5 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
    @enderror
</div>
