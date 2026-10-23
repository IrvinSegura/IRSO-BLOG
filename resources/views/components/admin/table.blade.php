{{-- Card with a table; "head" holds the <th> cells and "footer" usually the pagination links. --}}
@props(['head', 'title' => null, 'description' => null])

<div {{ $attributes->class('overflow-hidden rounded-2xl border border-gray-200/80 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900') }}>
    @if ($title)
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 px-5 py-4 dark:border-gray-800">
            <div>
                <h2 class="text-base font-semibold text-gray-900 dark:text-white">{{ $title }}</h2>
                @if ($description)
                    <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">{{ $description }}</p>
                @endif
            </div>
            {{ $toolbar ?? '' }}
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm [&_td]:whitespace-nowrap [&_td]:px-5 [&_td]:py-3.5 [&_th]:px-5 [&_th]:py-3">
            <thead class="border-b border-gray-100 bg-gray-50/70 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:border-gray-800 dark:bg-white/[0.02] dark:text-gray-400">
                <tr>{{ $head }}</tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-700 dark:divide-gray-800 dark:text-gray-300 [&>tr]:transition-colors [&>tr:hover]:bg-gray-50/60 dark:[&>tr:hover]:bg-white/[0.02]">
                {{ $slot }}
            </tbody>
        </table>
    </div>

    @isset($footer)
        @if (trim($footer) !== '')
            <div class="border-t border-gray-100 px-5 py-3 dark:border-gray-800">{{ $footer }}</div>
        @endif
    @endisset
</div>
