@props(['role'])

@php
    $color = match ($role?->id) {
        \App\Models\Role::ADMIN => 'yellow',
        \App\Models\Role::EDITOR => 'indigo',
        \App\Models\Role::USER => 'gray',
        null => 'gray',
        default => ['emerald', 'sky', 'violet', 'rose'][$role->id % 4],
    };
@endphp

<x-admin.badge :color="$color" dot {{ $attributes }}>{{ $role?->name ?? 'Sin rol' }}</x-admin.badge>
