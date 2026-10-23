<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Publication;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show the panel overview with totals and the latest activity.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $stats = [
            'publications' => Publication::count(),
            'users' => User::count(),
            'comments' => Comment::count(),
            'categories' => Category::count(),
            'publicationsThisMonth' => Publication::where('created_at', '>=', now()->startOfMonth())->count(),
            'usersThisMonth' => User::where('created_at', '>=', now()->startOfMonth())->count(),
            'myPublications' => $user->publications()->count(),
            'commentsOnMine' => Comment::whereIn('publication_id', $user->publications()->select('id'))->count(),
        ];

        // Regular users only see what they wrote; editors and administrators see the whole blog.
        $latestPublications = Publication::with(['category', 'user'])
            ->when(! $user->canManageContent(), fn ($query) => $query->whereBelongsTo($user))
            ->withCount('comments')
            ->latest()
            ->take(5)
            ->get();

        // The member list is only shown to administrators.
        $latestUsers = $user->isAdmin() ? User::with('role')->latest()->take(5)->get() : collect();

        $topCategories = Category::withCount('publications')
            ->orderByDesc('publications_count')
            ->take(5)
            ->get()
            ->filter(fn (Category $category) => $category->publications_count > 0);

        return view('dashboard', compact('stats', 'latestPublications', 'latestUsers', 'topCategories'));
    }
}
