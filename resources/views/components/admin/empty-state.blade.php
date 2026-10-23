@props(['icon' => 'inbox', 'title', 'description' => null])

<div {{ $attributes->class('flex flex-col items-center justify-center px-6 py-14 text-center') }}>
    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 text-gray-400 ring-8 ring-gray-50 dark:bg-gray-800 dark:text-gray-500 dark:ring-gray-800/40">
        <x-admin.icon :name="$icon" class="h-6 w-6" />
    </div>
    <h3 class="mt-5 text-sm font-semibold text-gray-900 dark:text-white">{{ $title }}</h3>
    @if ($description)
        <p class="mt-1 max-w-sm text-sm text-gray-500 dark:text-gray-400">{{ $description }}</p>
    @endif
    @if (! $slot->isEmpty())
        <div class="mt-5">{{ $slot }}</div>
    @endif
</div>
