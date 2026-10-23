<x-layouts.admin title="Panel">
    <div class="rounded-lg bg-white p-6 shadow">
        <h2 class="text-lg font-semibold text-gray-900">Bienvenido, {{ auth()->user()->name }}</h2>
        <p class="mt-1 text-sm text-gray-600">Administra las publicaciones, usuarios, categorías y roles del blog.</p>

        <div class="mt-6 flex flex-wrap gap-3">
            <x-link-button href="{{ route('admin.publications.create') }}">Crear publicación</x-link-button>
            <x-link-button href="{{ route('blog.index') }}" secondary>Ver blog</x-link-button>
        </div>
    </div>
</x-layouts.admin>
