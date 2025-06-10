<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use App\Models\Comment;


class CommentController extends Controller
{
    public function store(Request $request, Post $post): RedirectResponse
    {
        $request->validate([
            'content' => 'required|max:1000',
        ]);

        $post->comments()->create([
            'user_id' => auth()->id(),
            'content' => $request->content,
            'parent_id' => $request->parent_id,
        ]);

        return back()->with('success', 'Hozzászólás elküldve!');
    }

    public function destroy(Comment $comment)
    {
        // csak saját vagy admin törölhet
        if (auth()->user()->id !== $comment->user_id && !auth()->user()->is_admin) {
            abort(403);
        }

        // ha van válasz, töröljük azokat is
        $comment->replies()->delete();
        $comment->delete();

        return back()->with('success', 'Hozzászólás törölve!');
    }
}
