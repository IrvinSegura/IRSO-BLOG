@props(['title', 'subtitle'])

<x-guest-layout>
    <div class="flex min-h-screen bg-white">
        {{-- Brand panel, hidden on small screens --}}
        <aside class="relative hidden w-1/2 overflow-hidden bg-gray-900 lg:flex lg:flex-col lg:justify-between p-12 text-white">
            <div class="pointer-events-none absolute inset-0">
                <div class="absolute -left-24 -top-24 h-96 w-96 rounded-full bg-yellow-500/30 blur-3xl"></div>
                <div class="absolute -bottom-32 -right-16 h-[28rem] w-[28rem] rounded-full bg-indigo-600/30 blur-3xl"></div>
                <div class="absolute inset-0 bg-[radial-gradient(rgba(255,255,255,0.08)_1px,transparent_1px)] [background-size:22px_22px]"></div>
            </div>

            <a href="{{ route('blog.index') }}" class="relative flex items-center gap-3 text-lg font-semibold tracking-wide">
                <x-application-mark class="h-9 w-auto" />
                IRSO BLOG
            </a>

            <div class="relative max-w-md">
                <span class="inline-block rounded-full bg-yellow-500/15 px-3 py-1 text-xs font-semibold uppercase tracking-widest text-yellow-400 ring-1 ring-inset ring-yellow-500/30">
                    {{ __('Bienvenido') }}
                </span>
                <p class="mt-6 text-4xl font-semibold leading-tight">
                    {{ __('Comparte ideas, historias y lo que te inspira.') }}
                </p>
                <p class="mt-4 text-base leading-relaxed text-gray-400">
                    {{ __('Publica, administra tus artículos y sigue la conversación con tu comunidad.') }}
                </p>
            </div>

            <p class="relative text-sm text-gray-500">&copy; {{ date('Y') }} IRSO BLOG</p>
        </aside>

        {{-- Form panel --}}
        <main class="flex w-full items-center justify-center px-4 py-12 sm:px-8 lg:w-1/2">
            <div class="w-full max-w-sm">
                <a href="{{ route('blog.index') }}" class="mb-10 flex items-center gap-3 text-lg font-semibold text-gray-900 lg:hidden">
                    <x-application-mark class="h-9 w-auto" />
                    IRSO BLOG
                </a>

                <h1 class="text-2xl font-semibold tracking-tight text-gray-900">{{ $title }}</h1>
                <p class="mt-2 text-sm text-gray-500">{{ $subtitle }}</p>

                @if ($errors->any())
                    <div class="mt-6 flex gap-3 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700" role="alert">
                        <svg class="mt-0.5 h-5 w-5 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                        </svg>
                        <ul class="space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @session('status')
                    <div class="mt-6 rounded-lg border border-green-200 bg-green-50 p-4 text-sm font-medium text-green-700">
                        {{ $value }}
                    </div>
                @endsession

                {{ $slot }}
            </div>
        </main>
    </div>
</x-guest-layout>
