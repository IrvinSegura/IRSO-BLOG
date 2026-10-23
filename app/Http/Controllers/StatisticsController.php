<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Publication;
use App\Models\Role;
use App\Models\User;
use Illuminate\View\View;

class StatisticsController extends Controller
{
    /**
     * Show totals and charts about users, roles and publications.
     */
    public function index(): View
    {
        $roles = Role::withCount('users')->orderBy('id')->get(['id', 'name']);

        $categories = Category::withCount('publications')
            ->orderByDesc('publications_count')
            ->take(8)
            ->get(['id', 'name']);

        // Grouped in PHP so it works the same on SQLite and MySQL.
        $since = now()->subMonths(5)->startOfMonth();
        $perMonth = Publication::where('created_at', '>=', $since)
            ->pluck('created_at')
            ->countBy(fn ($date) => $date->format('Y-m'));

        $months = collect(range(5, 0))->map(fn (int $ago) => now()->subMonths($ago)->startOfMonth());
        $publicationsPerMonth = [
            'labels' => $months->map(fn ($month) => ucfirst($month->locale('es')->isoFormat('MMM YY')))->values(),
            'values' => $months->map(fn ($month) => $perMonth->get($month->format('Y-m'), 0))->values(),
        ];

        $totals = [
            'users' => User::count(),
            'publications' => Publication::count(),
            'comments' => Comment::count(),
            'commentsPerPublication' => round(Comment::count() / max(1, Publication::count()), 1),
        ];

        return view('admin.statistics', compact('roles', 'categories', 'publicationsPerMonth', 'totals'));
    }
}
