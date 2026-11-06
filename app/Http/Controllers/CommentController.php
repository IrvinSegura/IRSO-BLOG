<?php

namespace App\Http\Controllers;

use App\Models\Publication;
use App\Notifications\CommentReceived;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Add a comment from the authenticated user to the given publication.
     */
    public function store(Request $request, Publication $publication): RedirectResponse
    {
        $data = $request->validate([
            'comment' => ['required', 'string', 'max:255'],
        ]);

        $comment = $publication->comments()->create([
            ...$data,
            'user_id' => $request->user()->id,
        ]);

        $author = $publication->user;

        // Authors answering on their own publication don't need to be told.
        if ($author && ! $author->trashed() && ! $author->is($request->user())) {
            $author->notifyBestEffort(new CommentReceived($publication, $comment, $request->user()));
        }

        return back()->with('success', 'Comentario publicado');
    }
}
