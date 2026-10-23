{{-- Form shared by "Nueva publicación" and "Editar publicación"; pass $publication only when editing. --}}
@php
    $publication ??= null;
    $editing = (bool) $publication?->exists;
    $author = $publication?->user ?? auth()->user();
@endphp

{{-- The slug getter mirrors Str::slug() closely enough for a preview; the final slug is built on the server. --}}
<form
    method="POST"
    action="{{ $editing ? route('admin.publications.update', $publication) : route('admin.publications.store') }}"
    enctype="multipart/form-data"
    class="grid gap-6 lg:grid-cols-5"
    x-data="{
        title: @js(old('title', $publication?->title ?? '')),
        content: @js(old('content', $publication?->content ?? '')),
        category: '',
        original: @js($publication?->src_img_url),
        preview: @js($publication?->src_img_url),
        dragging: false,
        fileInfo: '',
        imageError: '',
        maxBytes: {{ \App\Models\Publication::MAX_IMAGE_KB * 1024 }},
        get slug() {
            return this.title.toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '')
                .replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
        },
        {{-- Checked here too so a big file is rejected before the whole form is sent and lost. --}}
        pickImage(files) {
            const [file] = files;
            this.imageError = '';
            if (! file) return;
            if (! file.type.startsWith('image/')) {
                this.rejectImage('El archivo debe ser una imagen (JPG, PNG, WebP o GIF).');
                return;
            }
            if (file.size > this.maxBytes) {
                this.rejectImage(`La imagen pesa ${(file.size / 1048576).toFixed(1)} MB y el máximo es ${this.maxBytes / 1048576} MB. Elige una más ligera.`);
                return;
            }
            this.$refs.image.files = files;
            this.preview = URL.createObjectURL(file);
            this.fileInfo = `${file.name} · ${(file.size / 1048576).toFixed(1)} MB`;
        },
        rejectImage(message) {
            this.imageError = message;
            this.$refs.image.value = '';
            this.preview = this.original;
            this.fileInfo = '';
        },
        dropImage(event) {
            this.dragging = false;
            this.pickImage(event.dataTransfer.files);
        },
    }"
    x-on:submit="if (imageError) $event.preventDefault()"
    x-on:option-selected="if ($event.detail.name === 'category_id') category = $event.detail.label"
>
    @csrf
    @if ($editing)
        @method('PUT')
    @endif

    <div class="space-y-6 lg:col-span-3">
        <section class="rounded-2xl border border-gray-200/80 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Contenido</h2>
            <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">El título y la descripción que verán tus lectores.</p>

            <div class="mt-6 space-y-5">
                <div>
                    <x-admin.field name="title" label="Título" x-model="title" required maxlength="255" placeholder="Ej. 10 ideas para escribir mejor" />
                    <p class="mt-2 flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400">
                        <x-admin.icon name="link" class="h-3.5 w-3.5" />
                        irsoblog/<span class="font-medium text-gray-700 dark:text-gray-300" x-text="slug || 'tu-publicacion'"></span>
                    </p>
                </div>

                <x-admin.select name="category_id" label="Categoría" placeholder="Elige una categoría"
                    :options="$categories->pluck('name', 'id')" :selected="old('category_id', $publication?->category_id)" />

                <div>
                    <div class="flex items-center justify-between">
                        <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Descripción</label>
                        <span class="text-xs" x-bind:class="content.length > 230 ? 'text-amber-600' : 'text-gray-400'" x-text="`${content.length}/255`"></span>
                    </div>
                    <textarea id="content" name="content" rows="5" maxlength="255" required x-model="content" placeholder="Cuenta de qué trata tu publicación…"
                        class="mt-1.5 block w-full rounded-lg border-gray-300 py-2.5 text-sm text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 dark:border-gray-700 dark:bg-gray-950 dark:text-white dark:focus:border-gray-400 dark:focus:ring-white/10"></textarea>
                    @error('content')
                        <p class="mt-1.5 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </section>

        <section class="rounded-2xl border border-gray-200/80 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Imagen de portada</h2>
            <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                @if ($editing)
                    Se conserva la imagen actual. Elige otra solo si quieres cambiarla (hasta {{ \App\Models\Publication::MAX_IMAGE_KB / 1024 }} MB).
                @else
                    JPG, PNG o WebP de hasta {{ \App\Models\Publication::MAX_IMAGE_KB / 1024 }} MB. Se recomienda formato horizontal.
                @endif
            </p>

            <label for="src_img"
                x-on:dragover.prevent="dragging = true" x-on:dragleave.prevent="dragging = false" x-on:drop.prevent="dropImage"
                class="mt-5 flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed px-6 py-10 text-center transition"
                x-bind:class="imageError ? 'border-red-300 bg-red-50/40 dark:border-red-500/40 dark:bg-red-500/5' : (dragging ? 'border-gray-900 bg-gray-50 dark:border-gray-400 dark:bg-gray-800' : 'border-gray-300 hover:border-gray-400 hover:bg-gray-50/60 dark:border-gray-700 dark:bg-gray-950 dark:hover:bg-white/[0.02]')">
                <template x-if="! preview">
                    <div>
                        <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                            <x-admin.icon name="photo" class="h-6 w-6" />
                        </span>
                        <p class="mt-4 text-sm font-medium text-gray-900 dark:text-white">Arrastra una imagen o <span class="text-yellow-700 underline dark:text-yellow-400">búscala en tu equipo</span></p>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Máximo {{ \App\Models\Publication::MAX_IMAGE_KB / 1024 }} MB</p>
                    </div>
                </template>
                <template x-if="preview">
                    <div class="w-full">
                        <img x-bind:src="preview" alt="Vista previa" class="mx-auto max-h-64 rounded-lg object-cover shadow-sm" />
                        <p class="mt-3 text-xs font-medium text-gray-700 dark:text-gray-300" x-text="fileInfo || (preview === original ? 'Imagen actual' : '')"></p>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Haz clic o arrastra otra imagen para cambiarla</p>
                    </div>
                </template>
                <input id="src_img" name="src_img" type="file" accept="image/*" class="sr-only" @required(! $editing) x-ref="image"
                    x-on:change="pickImage($event.target.files)" />
            </label>
            <p x-show="imageError" x-text="imageError" x-cloak class="mt-2 flex items-center gap-1.5 text-sm font-medium text-red-600 dark:text-red-400"></p>
            @error('src_img')
                <p class="mt-2 text-sm font-medium text-red-600 dark:text-red-400">{{ $message }} Vuelve a elegir la imagen: por seguridad, el navegador no la conserva al recargar.</p>
            @enderror
        </section>
    </div>

    {{-- Live preview --}}
    <aside class="lg:col-span-2">
        <div class="space-y-4 lg:sticky lg:top-24">
            <p class="text-xs font-semibold uppercase tracking-widest text-gray-500 dark:text-gray-400">Vista previa</p>
            <article class="overflow-hidden rounded-2xl border border-gray-200/80 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div class="relative aspect-[16/10] bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-800 dark:to-gray-900">
                    <img x-show="preview" x-bind:src="preview" alt="" class="h-full w-full object-cover" x-cloak />
                    <div x-show="! preview" class="flex h-full items-center justify-center text-gray-400">
                        <x-admin.icon name="photo" class="h-10 w-10" />
                    </div>
                    <span x-show="category" x-text="category" class="absolute left-4 top-4 rounded-full bg-white/90 px-3 py-1 text-xs font-semibold text-gray-900 shadow-sm backdrop-blur"></span>
                </div>
                <div class="p-5">
                    <h3 class="text-lg font-semibold leading-snug text-gray-900 dark:text-white" x-text="title || 'El título de tu publicación'"
                        x-bind:class="! title && 'opacity-40'"></h3>
                    <p class="mt-2 line-clamp-3 text-sm text-gray-600 dark:text-gray-400" x-text="content || 'Aquí aparecerá la descripción de tu publicación.'"
                        x-bind:class="! content && 'opacity-50'"></p>
                    <div class="mt-4 flex items-center gap-2.5 border-t border-gray-100 pt-4 dark:border-gray-800">
                        <x-admin.avatar :name="$author->name" size="sm" />
                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ $author->name }}</span>
                        <span class="text-xs text-gray-400">· {{ $editing ? $publication->created_at?->locale('es')->diffForHumans() : 'Ahora' }}</span>
                    </div>
                </div>
            </article>

            <div class="flex gap-3">
                <x-admin.button :href="route('admin.publications.mine')" variant="secondary" class="flex-1">Cancelar</x-admin.button>
                <x-admin.button :icon="$editing ? 'check' : 'send'" class="flex-1">{{ $editing ? 'Guardar cambios' : 'Publicar' }}</x-admin.button>
            </div>
        </div>
    </aside>
</form>
