@php($comments = $publication->comments()->count())

<x-layouts.admin title="Editar publicación"
    :description="'Publicada '.$publication->created_at?->locale('es')->diffForHumans().' · '.$comments.' '.($comments === 1 ? 'comentario' : 'comentarios')">
    <x-slot name="actions">
        <x-admin.button :href="route('admin.publications.mine')" variant="secondary" icon="arrow-left">Mis publicaciones</x-admin.button>
    </x-slot>

    @include('admin.publications.form', ['publication' => $publication])
</x-layouts.admin>
