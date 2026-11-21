<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Complaint;
use App\Models\Publication;
use App\Models\Reaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    private const PER_PAGE = 10;

    /**
     * Show the blog front page: the most talked-about publications and the newest ones,
     * or the publications that match a search and/or a category.
     */
    public function index(Request $request): View
    {
        $search = mb_substr(trim($request->string('q')->toString()), 0, 100);
        $category = $request->filled('categoria') ? Category::find($request->integer('categoria')) : null;
        $filtering = $search !== '' || $category !== null;
        // The highlights only open the front page; further pages are just the list
        $showTrending = ! $filtering && $request->integer('page', 1) <= 1;

        $trending = ! $showTrending ? collect() : Publication::with(['category', 'user'])
            ->withCount(['comments', 'reactions'])
            ->orderByDesc('comments_count')
            ->orderByDesc('reactions_count')
            ->latest()
            ->take(4)
            ->get();

        $latest = Publication::with(['category', 'user'])
            ->withCount(['comments', 'reactions'])
            ->search($search)
            ->when($category, fn ($query) => $query->where('category_id', $category->id))
            ->latest()
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        $categories = Category::withCount('publications')->orderByDesc('publications_count')->orderBy('name')->get();
        $savedIds = $request->user()->savedPublications()->pluck('publication_id')->flip();

        return view('blog.index', compact('trending', 'latest', 'categories', 'search', 'category', 'filtering', 'showTrending', 'savedIds'));
    }

    /**
     * Show one publication with its comments, reactions and related reading.
     */
    public function show(Request $request, Publication $publication, ?string $slug = null): View|RedirectResponse
    {
        // Old or hand-typed slugs still work, but the address bar always ends up on the real one
        if ($slug !== $publication->slug && $publication->slug) {
            return redirect()->to($publication->url(), 301);
        }

        $user = $request->user();
        $publication->load(['category', 'user', 'comments' => fn ($query) => $query->with('user')->oldest()]);

        $reactionCounts = $publication->reactions()
            ->selectRaw('type_reaction, COUNT(*) as total')
            ->groupBy('type_reaction')
            ->pluck('total', 'type_reaction');
        $reactions = collect(Reaction::TYPES)->map(fn ($type, $key) => [
            ...$type,
            'key' => $key,
            'count' => (int) ($reactionCounts[$key] ?? 0),
        ])->values();

        $myReaction = $publication->reactions()->where('user_id', $user->id)->value('type_reaction');
        $isSaved = $publication->saves()->where('user_id', $user->id)->exists();
        $hasReported = $publication->complaints()->where('user_id', $user->id)->exists();

        $related = Publication::with(['category', 'user'])->withCount(['comments', 'reactions'])
            ->whereKeyNot($publication->id)
            ->where('category_id', $publication->category_id)
            ->latest()
            ->take(3)
            ->get();

        return view('blog.show', [
            'publication' => $publication,
            'reactions' => $reactions,
            'myReaction' => $myReaction,
            'isSaved' => $isSaved,
            'hasReported' => $hasReported,
            'isOwn' => $publication->user_id === $user->id,
            'related' => $related,
            'reportReasons' => Complaint::REASONS,
        ]);
    }

    /**
     * Show the publications the reader saved for later, newest save first.
     */
    public function saved(Request $request): View
    {
        $saves = $request->user()->savedPublications()
            ->whereHas('publication')
            ->with(['publication' => fn ($query) => $query->with(['category', 'user'])->withCount(['comments', 'reactions'])])
            ->latest()
            ->paginate(12);

        return view('blog.saved', compact('saves'));
    }
}
