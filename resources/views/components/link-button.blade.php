@props(['secondary' => false])

<a {{ $attributes->class([
    'inline-flex items-center px-4 py-2 border rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150',
    'bg-gray-800 border-transparent text-white hover:bg-gray-700 active:bg-gray-900' => ! $secondary,
    'bg-white border-gray-300 text-gray-700 shadow-sm hover:bg-gray-50' => $secondary,
]) }}>
    {{ $slot }}
</a>
