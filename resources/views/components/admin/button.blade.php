{{-- Renders a link when "href" is given, otherwise a button. --}}
@props(['variant' => 'primary', 'size' => 'md', 'icon' => null, 'href' => null])

@php
    $classes = \Illuminate\Support\Arr::toCssClasses([
        'inline-flex items-center justify-center gap-2 rounded-lg font-semibold transition focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50',
        'px-3 py-1.5 text-xs' => $size === 'sm',
        'px-4 py-2.5 text-sm' => $size === 'md',
        'bg-gray-900 text-white shadow-sm hover:bg-gray-800 focus-visible:ring-gray-900 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-200 dark:focus-visible:ring-white dark:focus-visible:ring-offset-gray-950' => $variant === 'primary',
        'border border-gray-200 bg-white text-gray-700 shadow-sm hover:bg-gray-50 hover:text-gray-900 focus-visible:ring-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800 dark:hover:text-white dark:focus-visible:ring-offset-gray-950' => $variant === 'secondary',
        'bg-red-600 text-white shadow-sm hover:bg-red-500 focus-visible:ring-red-500 dark:focus-visible:ring-offset-gray-950' => $variant === 'danger',
        'bg-emerald-600 text-white shadow-sm hover:bg-emerald-500 focus-visible:ring-emerald-500 dark:focus-visible:ring-offset-gray-950' => $variant === 'success',
        'text-gray-600 hover:bg-gray-100 hover:text-gray-900 focus-visible:ring-gray-300 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white dark:focus-visible:ring-offset-gray-950' => $variant === 'ghost',
    ]);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>
        @if ($icon)<x-admin.icon :name="$icon" @class(['h-4 w-4' => $size === 'sm', 'h-5 w-5 -ml-0.5' => $size === 'md']) />@endif
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['type' => 'submit'])->class($classes) }}>
        @if ($icon)<x-admin.icon :name="$icon" @class(['h-4 w-4' => $size === 'sm', 'h-5 w-5 -ml-0.5' => $size === 'md']) />@endif
        {{ $slot }}
    </button>
@endif
