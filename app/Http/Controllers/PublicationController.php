<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Publication;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PublicationController extends Controller
{
    /**
     * Show the publications with their comments.
     */
    public function index(): View
    {
        $publications = Publication::with(['category', 'user', 'comments.user'])
            ->latest()
            ->paginate(10);

        return view('admin.publications.index', compact('publications'));
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
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:category,id'],
            'content' => ['required', 'string', 'max:255'],
            'src_img' => ['required', 'image', 'max:2048'],
        ]);

        $request->user()->publications()->create([
            ...$data,
            'slug' => Str::slug($data['title']),
            'status' => Publication::STATUS_PUBLISHED,
            'src_img' => $request->file('src_img')->store('publications', 'public'),
        ]);

        return to_route('admin.publications.index')->with('success', 'Publicación creada correctamente');
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
}
