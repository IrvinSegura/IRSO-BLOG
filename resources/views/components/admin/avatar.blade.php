{{-- Initials on a color picked from the name, so each person keeps the same color. --}}
@props(['name' => '', 'size' => 'md'])

@php
    $palette = ['bg-amber-500', 'bg-indigo-500', 'bg-emerald-500', 'bg-rose-500', 'bg-sky-500', 'bg-violet-500', 'bg-orange-500', 'bg-teal-500'];
    $initials = collect(preg_split('/\s+/', trim((string) $name)))
        ->filter()
        ->take(2)
        ->map(fn ($word) => mb_strtoupper(mb_substr($word, 0, 1)))
        ->implode('') ?: '?';
    $color = $palette[crc32((string) $name) % count($palette)];
@endphp

<span {{ $attributes->class([
    'inline-flex shrink-0 select-none items-center justify-center rounded-full font-semibold text-white ring-2 ring-white dark:ring-gray-900',
    $color,
    'h-7 w-7 text-[11px]' => $size === 'sm',
    'h-9 w-9 text-xs' => $size === 'md',
    'h-11 w-11 text-sm' => $size === 'lg',
]) }} aria-hidden="true">{{ $initials }}</span>
