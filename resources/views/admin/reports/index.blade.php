@php
    $tabs = [
        \App\Models\Complaint::STATUS_PENDING => ['label' => 'Pendientes', 'color' => 'yellow'],
        \App\Models\Complaint::STATUS_RESOLVED => ['label' => 'Resueltos', 'color' => 'emerald'],
        \App\Models\Complaint::STATUS_DISMISSED => ['label' => 'Descartados', 'color' => 'gray'],
    ];
    $reasonColors = ['spam' => 'sky', 'harassment' => 'red', 'inappropriate' => 'rose', 'misinformation' => 'violet', 'other' => 'gray'];
    $pending = (int) ($counts[\App\Models\Complaint::STATUS_PENDING] ?? 0);
@endphp

<x-layouts.admin title="Reportes" :description="$pending === 0 ? 'No hay reportes pendientes. ¡Todo en orden!' : $pending.' '.($pending === 1 ? 'reporte espera' : 'reportes esperan').' revisión'">
    <nav class="mb-6 flex gap-1 overflow-x-auto rounded-xl bg-gray-100 p-1 dark:bg-gray-800/60" aria-label="Estado de los reportes">
        @foreach ($tabs as $value => $tab)
            <a href="{{ route('admin.reports.index', $value === \App\Models\Complaint::STATUS_PENDING ? [] : ['estado' => $value]) }}"
                @class([
                    'flex flex-1 items-center justify-center gap-2 whitespace-nowrap rounded-lg px-4 py-2 text-sm font-medium transition',
                    'bg-white text-gray-900 shadow-sm dark:bg-gray-900 dark:text-white' => $status === $value,
                    'text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white' => $status !== $value,
                ])
                @if ($status === $value) aria-current="page" @endif>
                {{ $tab['label'] }}
                <x-admin.badge :color="$status === $value ? $tab['color'] : 'gray'">{{ (int) ($counts[$value] ?? 0) }}</x-admin.badge>
            </a>
        @endforeach
    </nav>

    @if ($complaints->isEmpty())
        <div class="rounded-2xl border border-dashed border-gray-300 bg-white dark:border-gray-700 dark:bg-gray-900">
            <x-admin.empty-state :icon="$status === 'pending' ? 'check' : 'flag'"
                :title="$status === 'pending' ? 'Sin reportes pendientes' : 'Nada por aquí'"
                :description="$status === 'pending' ? 'Cuando un lector reporte una publicación aparecerá aquí para que la revises.' : 'Todavía no hay reportes en este estado.'" />
        </div>
    @else
        <ul class="space-y-4">
            @foreach ($complaints as $complaint)
                @php($publication = $complaint->publication)
                <li class="rounded-2xl border border-gray-200/80 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="flex flex-col gap-4 xl:flex-row xl:items-start">
                        <div class="flex min-w-0 flex-1 gap-4">
                            @if ($publication)
                                <img src="{{ $publication->src_img_url }}" alt="" loading="lazy" class="hidden h-20 w-28 shrink-0 rounded-xl object-cover sm:block" />
                            @endif
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <x-admin.badge :color="$reasonColors[$complaint->reason] ?? 'gray'">{{ $complaint->reasonLabel() }}</x-admin.badge>
                                    @if (($publication?->complaints_count ?? 0) > 1)
                                        <x-admin.badge color="red">{{ $publication->complaints_count }} reportes</x-admin.badge>
                                    @endif
                                    @if ($publication?->trashed())
                                        <x-admin.badge color="gray">Retirada</x-admin.badge>
                                    @endif
                                </div>
                                <p class="mt-2 truncate font-semibold text-gray-900 dark:text-white">
                                    @if ($publication && ! $publication->trashed())
                                        <a href="{{ $publication->url() }}" class="hover:underline" target="_blank" rel="noopener">{{ $publication->title }}</a>
                                    @else
                                        {{ $publication?->title ?? 'Publicación eliminada' }}
                                    @endif
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">de {{ $publication?->user?->name ?? 'autor desconocido' }}</p>
                                @if ($complaint->description)
                                    <blockquote class="mt-3 border-l-2 border-gray-200 pl-3 text-sm text-gray-700 dark:border-gray-700 dark:text-gray-300">{{ $complaint->description }}</blockquote>
                                @endif
                                <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                                    Reportado por {{ $complaint->user?->name ?? 'usuario eliminado' }} · {{ $complaint->created_at?->locale('es')->diffForHumans() }}
                                    @if ($complaint->resolved_at)
                                        · Revisado por {{ $complaint->resolver?->name ?? '—' }} {{ $complaint->resolved_at->locale('es')->diffForHumans() }}
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="flex shrink-0 flex-wrap gap-2">
                            @if ($status === 'pending')
                                <form method="POST" action="{{ route('admin.reports.update', $complaint) }}">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="action" value="dismiss">
                                    <x-admin.button variant="ghost" size="sm" icon="x">Descartar</x-admin.button>
                                </form>
                                <form method="POST" action="{{ route('admin.reports.update', $complaint) }}">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="action" value="resolve">
                                    <x-admin.button variant="secondary" size="sm" icon="check">Resuelto</x-admin.button>
                                </form>
                                @if ($publication && ! $publication->trashed())
                                    <x-admin.button type="button" variant="danger" size="sm" icon="eye-slash"
                                        x-on:click="$dispatch('open-modal', 'remove-publication-{{ $complaint->id }}')">Retirar publicación</x-admin.button>

                                    <x-admin.modal name="remove-publication-{{ $complaint->id }}" title="¿Retirar «{{ $publication->title }}»?"
                                        description="Dejará de verse en el blog y se cerrarán todos sus reportes pendientes. Podrás restaurarla desde «Resueltos»." icon="warning">
                                        <form method="POST" action="{{ route('admin.reports.update', $complaint) }}" class="flex flex-col-reverse gap-2 pt-2 sm:flex-row sm:justify-end">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="action" value="remove">
                                            <x-admin.button type="button" variant="secondary" x-on:click="$dispatch('close-modal')">Cancelar</x-admin.button>
                                            <x-admin.button variant="danger">Retirar publicación</x-admin.button>
                                        </form>
                                    </x-admin.modal>
                                @endif
                            @elseif ($publication?->trashed())
                                <form method="POST" action="{{ route('admin.reports.restore-publication', $complaint) }}">
                                    @csrf @method('PATCH')
                                    <x-admin.button variant="secondary" size="sm" icon="restore">Restaurar publicación</x-admin.button>
                                </form>
                            @endif
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>

        <div class="mt-8">{{ $complaints->links() }}</div>
    @endif
</x-layouts.admin>
