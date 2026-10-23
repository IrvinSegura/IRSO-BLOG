<?php

namespace App\Http\Controllers;

use App\Models\Publication;
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

        $publication->comments()->create([
            ...$data,
            'user_id' => $request->user()->id,
        ]);

        return back()->with('success', 'Comentario publicado');
    }
}
