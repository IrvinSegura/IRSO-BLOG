{{-- Spanish pagination styled like the admin panel; replaces Laravel's default Tailwind view. --}}
@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Paginación" class="flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-gray-500 dark:text-gray-400">
            Mostrando <span class="font-medium text-gray-900 dark:text-white">{{ $paginator->firstItem() }}</span>
            a <span class="font-medium text-gray-900 dark:text-white">{{ $paginator->lastItem() }}</span>
            de <span class="font-medium text-gray-900 dark:text-white">{{ $paginator->total() }}</span>
        </p>

        <div class="flex items-center gap-1">
            @if ($paginator->onFirstPage())
                <span class="inline-flex h-9 w-9 cursor-not-allowed items-center justify-center rounded-lg text-gray-300 dark:text-gray-700" aria-disabled="true">
                    <x-admin.icon name="arrow-left" class="h-4 w-4" />
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-gray-500 transition hover:bg-white hover:text-gray-900 hover:shadow-sm dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white" aria-label="Anterior">
                    <x-admin.icon name="arrow-left" class="h-4 w-4" />
                </a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="inline-flex h-9 w-9 items-center justify-center text-sm text-gray-400">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="inline-flex h-9 min-w-9 items-center justify-center rounded-lg bg-gray-900 px-3 text-sm font-semibold text-white shadow-sm dark:bg-white dark:text-gray-900">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="inline-flex h-9 min-w-9 items-center justify-center rounded-lg px-3 text-sm font-medium text-gray-600 transition hover:bg-white hover:text-gray-900 hover:shadow-sm dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white" aria-label="Ir a la página {{ $page }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-gray-500 transition hover:bg-white hover:text-gray-900 hover:shadow-sm dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white" aria-label="Siguiente">
                    <x-admin.icon name="arrow-right" class="h-4 w-4" />
                </a>
            @else
                <span class="inline-flex h-9 w-9 cursor-not-allowed items-center justify-center rounded-lg text-gray-300 dark:text-gray-700" aria-disabled="true">
                    <x-admin.icon name="arrow-right" class="h-4 w-4" />
                </span>
            @endif
        </div>
    </nav>
@endif
