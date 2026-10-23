{{-- Labeled input or textarea with its validation error; use x-admin.select for choices. --}}
@props(['name', 'label', 'type' => 'text', 'id' => null, 'hint' => null])

@php
    $id ??= $name;
    $control = 'block w-full rounded-lg border-gray-300 bg-white text-sm text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 dark:border-gray-700 dark:bg-gray-950 dark:text-white dark:placeholder:text-gray-500 dark:focus:border-gray-400 dark:focus:ring-white/10';
@endphp

<div>
    <label for="{{ $id }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ $label }}</label>

    @if ($type === 'textarea')
        <textarea id="{{ $id }}" name="{{ $name }}" {{ $attributes->class([$control, 'mt-1.5 py-2.5']) }}>{{ $slot }}</textarea>
    @else
        <input id="{{ $id }}" name="{{ $name }}" type="{{ $type }}" {{ $attributes->class([$control, 'mt-1.5 py-2.5 read-only:bg-gray-50 read-only:text-gray-500 dark:read-only:bg-gray-900 dark:read-only:text-gray-400']) }} />
    @endif

    @if ($hint)
        <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">{{ $hint }}</p>
    @endif
    @error($name)
        <p class="mt-1.5 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
    @enderror
</div>
