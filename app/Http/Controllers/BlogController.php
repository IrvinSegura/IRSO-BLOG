<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Publication;
use Illuminate\View\View;

class BlogController extends Controller
{
    /**
     * Show the blog front page: the most commented publications and the newest ones.
     */
    public function index(): View
    {
        $trending = Publication::with(['category', 'user'])->withCount('comments')
            ->orderByDesc('comments_count')
            ->latest()
            ->take(4)
            ->get();
        $latest = Publication::with(['category', 'user'])->withCount('comments')->latest()->take(10)->get();
        $categories = Category::withCount('publications')->orderByDesc('publications_count')->orderBy('name')->get();

        return view('blog.index', compact('trending', 'latest', 'categories'));
    }
}
