{{-- Shows the "success" / "failed" session messages and any validation errors. --}}
@session('success')
    <div x-data="{ show: true }" x-show="show" class="mb-4 flex items-start justify-between gap-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800" role="status">
        <span>{{ $value }}</span>
        <button type="button" x-on:click="show = false" aria-label="Cerrar">&times;</button>
    </div>
@endsession

@session('failed')
    <div x-data="{ show: true }" x-show="show" class="mb-4 flex items-start justify-between gap-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
        <span>{{ $value }}</span>
        <button type="button" x-on:click="show = false" aria-label="Cerrar">&times;</button>
    </div>
@endsession

@if ($errors->any())
    <div class="mb-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
        <p class="font-medium">Revisa los datos enviados:</p>
        <ul class="mt-2 list-inside list-disc">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
