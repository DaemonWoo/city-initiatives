<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use App\Models\Initiative;
use App\Notifications\NewCommentNotification;
use Illuminate\Http\RedirectResponse;

class InitiativeCommentController extends Controller
{
    public function store(StoreCommentRequest $request, Initiative $initiative): RedirectResponse
    {
        $comment = $initiative->comments()->create([
            'user_id' => $request->user()->id,
            ...$request->safe()->only('body'),
        ]);
        $comment->load('user', 'initiative');
        $initiative->user
            ->notify(new NewCommentNotification($comment));

        return redirect()
            ->route('initiatives.show', $initiative)
            ->with('success', 'Comment added.');
    }

    public function destroy(Initiative $initiative, Comment $comment): RedirectResponse
    {
        abort_unless($comment->initiative_id === $initiative->id, 404);
        abort_unless($comment->user_id === auth()->id(), 403);

        $comment->delete();

        return redirect()
            ->route('initiatives.show', $initiative)
            ->with('success', 'Comment deleted.');
    }
}
