<?php

namespace App\Http\Controllers;

use App\Models\Publication;
use App\Models\Reaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReactionController extends Controller
{
    /**
     * Set, change or remove the reader's reaction. Picking the reaction they already have
     * removes it, like in the Nexus app.
     */
    public function store(Request $request, Publication $publication): JsonResponse|RedirectResponse
    {
        $type = $request->validate([
            'type' => ['required', 'string', Rule::in(array_keys(Reaction::TYPES))],
        ])['type'];

        $existing = $publication->reactions()->where('user_id', $request->user()->id)->first();

        if ($existing?->type_reaction === $type) {
            $existing->delete();
            $mine = null;
        } else {
            $publication->reactions()->updateOrCreate(
                ['user_id' => $request->user()->id],
                ['type_reaction' => $type],
            );
            $mine = $type;
        }

        if ($request->expectsJson()) {
            $counts = $publication->reactions()
                ->selectRaw('type_reaction, COUNT(*) as total')
                ->groupBy('type_reaction')
                ->pluck('total', 'type_reaction');

            return response()->json([
                'mine' => $mine,
                'counts' => collect(Reaction::TYPES)->keys()->mapWithKeys(fn ($key) => [$key => (int) ($counts[$key] ?? 0)]),
            ]);
        }

        return back();
    }
}
