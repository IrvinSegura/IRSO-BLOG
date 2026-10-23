<x-layouts.admin title="Nueva publicación">
    {{-- The slug getter mirrors Str::slug() closely enough for a preview; the final slug is built on the server. --}}
    <form
        method="POST"
        action="{{ route('admin.publications.store') }}"
        enctype="multipart/form-data"
        class="max-w-2xl space-y-5 rounded-lg bg-white p-6 shadow"
        x-data="{
            title: @js(old('title', '')),
            preview: null,
            get slug() {
                return this.title.toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '')
                    .replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
            },
            showPreview(event) {
                const [file] = event.target.files;
                this.preview = file ? URL.createObjectURL(file) : null;
            },
        }"
    >
        @csrf

        <div>
            <x-label for="title" value="Título" />
            <x-input id="title" name="title" type="text" class="mt-1 block w-full" x-model="title" required />
        </div>

        <div>
            <x-label for="slug" value="Slug" />
            <x-input id="slug" type="text" class="mt-1 block w-full bg-gray-50 text-gray-500" x-bind:value="slug" readonly />
        </div>

        <div>
            <x-label for="category_id" value="Categoría" />
            <x-select id="category_id" name="category_id" class="mt-1 block w-full" required>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                @endforeach
            </x-select>
        </div>

        <div>
            <x-label for="content" value="Descripción" />
            <textarea id="content" name="content" rows="4" maxlength="255" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>{{ old('content') }}</textarea>
        </div>

        <div>
            <x-label for="src_img" value="Imagen" />
            <input id="src_img" name="src_img" type="file" accept="image/*" class="mt-1 block w-full text-sm text-gray-700" x-on:change="showPreview" required />
            <img x-show="preview" x-bind:src="preview" alt="Vista previa" class="mt-3 h-32 rounded-md object-cover" x-cloak />
        </div>

        <div class="flex justify-end gap-2">
            <x-link-button href="{{ route('admin.publications.index') }}" secondary>Cancelar</x-link-button>
            <x-button>Publicar</x-button>
        </div>
    </form>
</x-layouts.admin>
