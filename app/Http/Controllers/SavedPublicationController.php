<?php

namespace App\Http\Controllers;

use App\Models\Publication;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SavedPublicationController extends Controller
{
    /**
     * Save the publication for later, or take it out of the saved list if it was there.
     */
    public function toggle(Request $request, Publication $publication): JsonResponse|RedirectResponse
    {
        $existing = $publication->saves()->where('user_id', $request->user()->id)->first();

        if ($existing) {
            $existing->delete();
        } else {
            $publication->saves()->create(['user_id' => $request->user()->id]);
        }

        $saved = $existing === null;
        $message = $saved ? 'Guardada en tu lista' : 'Quitada de guardados';

        if ($request->expectsJson()) {
            return response()->json(['saved' => $saved, 'message' => $message]);
        }

        return back()->with('success', $message);
    }
}
