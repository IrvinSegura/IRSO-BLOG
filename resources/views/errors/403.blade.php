<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Acceso restringido · IRSO BLOG</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <x-theme-script />

        @vite(['resources/css/app.css'])
    </head>
    <body class="flex min-h-screen items-center justify-center bg-gray-50 px-4 font-sans text-gray-900 antialiased dark:bg-gray-950 dark:text-gray-100">
        <main class="relative w-full max-w-md overflow-hidden rounded-3xl border border-gray-200 bg-white p-8 text-center shadow-sm dark:border-gray-800 dark:bg-gray-900 sm:p-10">
            <div class="pointer-events-none absolute -left-16 -top-16 h-48 w-48 rounded-full bg-yellow-200/40 blur-3xl dark:bg-yellow-500/10"></div>
            <div class="pointer-events-none absolute -bottom-16 -right-16 h-48 w-48 rounded-full bg-indigo-200/40 blur-3xl dark:bg-indigo-500/15"></div>

            <div class="relative">
                <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-gray-100 text-gray-700 ring-8 ring-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:ring-gray-800/40">
                    <x-admin.icon name="shield" class="h-7 w-7" />
                </span>
                <p class="mt-6 text-sm font-semibold uppercase tracking-widest text-amber-600 dark:text-amber-400">Error 403</p>
                <h1 class="mt-2 text-2xl font-semibold tracking-tight">Esta sección no está disponible para tu cuenta</h1>
                <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">
                    Tu rol no tiene permiso para entrar aquí. Si crees que es un error, pide a un administrador que revise tu rol.
                </p>

                <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:justify-center">
                    <a href="{{ url('/dashboard') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-800 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-200">
                        <x-admin.icon name="home" class="h-4 w-4" /> Ir al panel
                    </a>
                    <a href="{{ url('/blog') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm ring-1 ring-inset ring-gray-200 transition hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-200 dark:ring-gray-700 dark:hover:bg-gray-700">
                        <x-admin.icon name="globe" class="h-4 w-4" /> Ver blog
                    </a>
                </div>
            </div>
        </main>
    </body>
</html>
