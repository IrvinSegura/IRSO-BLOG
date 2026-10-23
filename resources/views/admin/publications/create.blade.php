<x-layouts.admin title="Nueva publicación" description="Completa los datos y revisa la vista previa antes de publicar.">
    <x-slot name="actions">
        <x-admin.button :href="route('admin.publications.mine')" variant="secondary" icon="document">Ver mis publicaciones</x-admin.button>
    </x-slot>

    @include('admin.publications.form')
</x-layouts.admin>
