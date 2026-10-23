<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Show the active categories.
     */
    public function index(): View
    {
        $categories = Category::orderBy('name')->paginate(10);

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Create a new category.
     */
    public function store(Request $request): RedirectResponse
    {
        Category::create($request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]));

        return back()->with('success', 'Categoría creada correctamente');
    }

    /**
     * Rename the given category.
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        $category->update($request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]));

        return back()->with('success', 'Categoría renombrada correctamente');
    }

    /**
     * Soft delete the given category when it has no publications.
     */
    public function destroy(Category $category): RedirectResponse
    {
        if ($category->publications()->exists()) {
            return back()->with('failed', 'No se puede eliminar la categoría porque tiene publicaciones asociadas');
        }

        $category->delete();

        return back()->with('success', 'Categoría eliminada correctamente');
    }

    /**
     * Restore a soft deleted category.
     */
    public function restore(Category $category): RedirectResponse
    {
        $category->restore();

        return back()->with('success', 'Categoría restaurada correctamente');
    }
}
