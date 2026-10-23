<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Publication;
use Illuminate\View\View;

class BlogController extends Controller
{
    /**
     * Show the blog front page with trending and latest publications.
     */
    public function index(): View
    {
        $trending = Publication::latest()->take(4)->get();
        $latest = Publication::with('user')->latest()->skip(4)->take(10)->get();
        $categories = Category::orderBy('name')->get();

        return view('blog.index', compact('trending', 'latest', 'categories'));
    }
}
