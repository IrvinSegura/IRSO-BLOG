@props(['label', 'value', 'icon', 'color' => 'gray', 'hint' => null, 'href' => null])

@php
    $tones = [
        'gray' => 'bg-gray-100 text-gray-700 dark:bg-white/5 dark:text-gray-300',
        'yellow' => 'bg-amber-100 text-amber-700 dark:bg-amber-400/10 dark:text-amber-300',
        'indigo' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-400/10 dark:text-indigo-300',
        'emerald' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-400/10 dark:text-emerald-300',
        'rose' => 'bg-rose-100 text-rose-700 dark:bg-rose-400/10 dark:text-rose-300',
        'sky' => 'bg-sky-100 text-sky-700 dark:bg-sky-400/10 dark:text-sky-300',
    ];
@endphp

<{{ $href ? 'a' : 'div' }} @if ($href) href="{{ $href }}" @endif
    {{ $attributes->class([
        'group relative overflow-hidden rounded-2xl border border-gray-200/80 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900',
        'transition hover:-translate-y-0.5 hover:shadow-md dark:hover:border-gray-700' => $href,
    ]) }}>
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $label }}</p>
            <p class="mt-2 text-3xl font-semibold tracking-tight text-gray-900 dark:text-white">{{ $value }}</p>
        </div>
        <span @class(['flex h-11 w-11 items-center justify-center rounded-xl', $tones[$color] ?? $tones['gray']])>
            <x-admin.icon :name="$icon" class="h-6 w-6" />
        </span>
    </div>
    @if ($hint)
        <p class="mt-4 flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400">
            {{ $hint }}
            @if ($href)
                <x-admin.icon name="arrow-right" class="h-3.5 w-3.5 transition group-hover:translate-x-0.5" />
            @endif
        </p>
    @endif
</{{ $href ? 'a' : 'div' }}>
