<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Publication;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PublicationController extends Controller
{
    /**
     * Show every publication with its comments; regular users only manage their own.
     */
    public function index(Request $request): View|RedirectResponse
    {
        if (! $request->user()->canManageContent()) {
            return to_route('admin.publications.mine');
        }

        $publications = Publication::with(['category', 'user', 'comments.user'])
            ->latest()
            ->paginate(10);

        return view('admin.publications.index', compact('publications'));
    }

    /**
     * Show the publications written by the authenticated user.
     */
    public function mine(Request $request): View
    {
        $publications = $request->user()->publications()
            ->with(['category', 'comments' => fn ($query) => $query->with('user')->latest()])
            ->withCount('comments')
            ->latest()
            ->paginate(10);

        return view('admin.publications.mine', compact('publications'));
    }

    /**
     * Show the form to create a publication.
     */
    public function create(): View
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.publications.create', compact('categories'));
    }

    /**
     * Store a new publication written by the authenticated user.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatePublication($request, imageRequired: true);

        $request->user()->publications()->create([
            ...$data,
            'slug' => Str::slug($data['title']),
            'status' => Publication::STATUS_PUBLISHED,
            'src_img' => $request->file('src_img')->store('publications', 'public'),
        ]);

        return to_route('admin.publications.mine')->with('success', 'Publicación creada correctamente');
    }

    /**
     * Show the form to edit a publication.
     */
    public function edit(Publication $publication): View
    {
        Gate::authorize('update', $publication);

        $categories = Category::orderBy('name')->get();

        return view('admin.publications.edit', compact('publication', 'categories'));
    }

    /**
     * Update a publication; the cover image only changes when a new one is uploaded.
     */
    public function update(Request $request, Publication $publication): RedirectResponse
    {
        Gate::authorize('update', $publication);

        $data = $this->validatePublication($request, imageRequired: false);
        $data['slug'] = Str::slug($data['title']);

        if ($request->hasFile('src_img')) {
            $previous = $publication->src_img;
            $data['src_img'] = $request->file('src_img')->store('publications', 'public');

            // Seeded publications point at external URLs, which are not ours to delete.
            if (! Str::startsWith($previous, ['http://', 'https://'])) {
                Storage::disk('public')->delete($previous);
            }
        } else {
            unset($data['src_img']);
        }

        $publication->update($data);

        $destination = $publication->user_id === $request->user()->id ? 'admin.publications.mine' : 'admin.publications.index';

        return to_route($destination)->with('success', 'Publicación actualizada correctamente');
    }

    /**
     * Download every publication as a JSON file.
     */
    public function export(): JsonResponse
    {
        return response()
            ->json(Publication::all(), options: JSON_PRETTY_PRINT)
            ->header('Content-Disposition', 'attachment; filename="publications.json"');
    }

    /**
     * Validate the publication form with Spanish messages.
     *
     * @return array<string, mixed>
     */
    private function validatePublication(Request $request, bool $imageRequired): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:category,id'],
            'content' => ['required', 'string', 'max:255'],
            'src_img' => [$imageRequired ? 'required' : 'nullable', 'image', 'max:'.Publication::MAX_IMAGE_KB],
        ], [
            'required' => 'El campo :attribute es obligatorio.',
            'max.string' => 'El campo :attribute no puede tener más de :max caracteres.',
            'src_img.image' => 'La portada debe ser una imagen (JPG, PNG, WebP o GIF).',
            'src_img.max' => 'La imagen pesa demasiado: el máximo es '.(Publication::MAX_IMAGE_KB / 1024).' MB.',
            'src_img.uploaded' => 'No se pudo subir la imagen. Prueba con una de menos de '.(Publication::MAX_IMAGE_KB / 1024).' MB.',
            'category_id.exists' => 'La categoría elegida no existe.',
        ], [
            'title' => 'título',
            'category_id' => 'categoría',
            'content' => 'descripción',
            'src_img' => 'imagen de portada',
        ]);
    }
}
