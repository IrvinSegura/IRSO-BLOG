{{-- Shows the "success" / "failed" session messages as toasts and any validation errors. --}}
@php
    $toasts = array_filter([
        'success' => session('success'),
        'failed' => session('failed'),
    ]);
@endphp

@if ($toasts)
    <div class="pointer-events-none fixed inset-x-4 top-4 z-[60] flex flex-col items-end gap-3 sm:left-auto sm:right-6 sm:top-6 sm:w-96" aria-live="polite">
        @foreach ($toasts as $type => $message)
            <div x-data="{ show: false }" x-init="$nextTick(() => show = true); setTimeout(() => show = false, 5000)"
                x-show="show" x-cloak
                x-transition:enter="ease-out duration-300" x-transition:enter-start="translate-y-2 opacity-0 sm:translate-x-4 sm:translate-y-0" x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="pointer-events-auto flex w-full items-start gap-3 rounded-xl bg-white p-4 shadow-lg ring-1 ring-gray-900/5 dark:bg-gray-900 dark:ring-white/10"
                role="{{ $type === 'success' ? 'status' : 'alert' }}">
                <span @class(['mt-0.5', 'text-emerald-500' => $type === 'success', 'text-red-500' => $type === 'failed'])>
                    <x-admin.icon :name="$type === 'success' ? 'check' : 'warning'" class="h-6 w-6" />
                </span>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $type === 'success' ? 'Listo' : 'Algo salió mal' }}</p>
                    <p class="mt-0.5 text-sm text-gray-600 dark:text-gray-400">{{ $message }}</p>
                </div>
                <button type="button" class="rounded-md p-1 text-gray-400 transition hover:text-gray-600" x-on:click="show = false">
                    <x-admin.icon name="x" class="h-4 w-4" />
                    <span class="sr-only">Cerrar</span>
                </button>
            </div>
        @endforeach
    </div>
@endif

@if ($errors->any())
    <div class="mb-6 flex gap-3 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800 dark:border-red-500/20 dark:bg-red-500/10 dark:text-red-300" role="alert">
        <x-admin.icon name="warning" class="h-5 w-5 shrink-0 text-red-500 dark:text-red-400" />
        <div>
            <p class="font-semibold">Revisa los datos enviados</p>
            <ul class="mt-1.5 list-inside list-disc space-y-0.5 text-red-700 dark:text-red-300/90">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
