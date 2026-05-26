<?php

namespace App\Http\Controllers;

use App\Models\Initiative;
use Illuminate\Http\RedirectResponse;

class InitiativeVoteController extends Controller
{
    public function store(Initiative $initiative): RedirectResponse
    {
        $initiative->votes()->firstOrCreate([
            'user_id' => auth()->id(),
        ]);

        return redirect()
            ->route('initiatives.show', $initiative)
            ->with('success', 'Vote submitted.');
    }
}
