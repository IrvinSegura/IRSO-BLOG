{{-- Square icon-only button for table actions; "label" is the tooltip and screen-reader text. --}}
@props(['icon', 'label', 'variant' => 'default'])

<button {{ $attributes->merge(['type' => 'button', 'title' => $label])->class([
    'inline-flex h-8 w-8 items-center justify-center rounded-lg transition focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-300',
    'text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white' => $variant === 'default',
    'text-gray-500 hover:bg-red-50 hover:text-red-600 dark:text-gray-400 dark:hover:bg-red-500/10 dark:hover:text-red-400' => $variant === 'danger',
    'text-gray-500 hover:bg-emerald-50 hover:text-emerald-600 dark:text-gray-400 dark:hover:bg-emerald-500/10 dark:hover:text-emerald-400' => $variant === 'success',
]) }}>
    <x-admin.icon :name="$icon" class="h-[18px] w-[18px]" />
    <span class="sr-only">{{ $label }}</span>
</button>
