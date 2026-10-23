{{-- Trash icon that asks for confirmation in a modal before sending the DELETE request. --}}
@props(['action', 'name', 'title', 'message'])

<x-admin.icon-button icon="trash" variant="danger" label="Eliminar" x-on:click="$dispatch('open-modal', '{{ $name }}')" />

<x-admin.modal :name="$name" :title="$title" :description="$message" icon="warning" tone="red">
    <form method="POST" action="{{ $action }}" class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
        @csrf
        @method('DELETE')
        <x-admin.button type="button" variant="secondary" x-on:click="$dispatch('close-modal')">Cancelar</x-admin.button>
        <x-admin.button variant="danger" icon="trash">Sí, eliminar</x-admin.button>
    </form>
</x-admin.modal>
