<?php

namespace App\Observers;

use App\Models\Initiative;
use Illuminate\Support\Facades\Cache;

class InitiativeObserver
{
    /**
     * Handle the Initiative "created" event.
     */
    public function created(Initiative $initiative): void
    {
        Cache::forget('initiatives_list_shared');
    }

    /**
     * Handle the Initiative "updated" event.
     */
    public function updated(Initiative $initiative): void
    {
        Cache::forget('initiatives_list_shared');
    }

    /**
     * Handle the Initiative "deleted" event.
     */
    public function deleted(Initiative $initiative): void
    {
        Cache::forget('initiatives_list_shared');
    }

    /**
     * Handle the Initiative "restored" event.
     */
    public function restored(Initiative $initiative): void
    {
        //
    }

    /**
     * Handle the Initiative "force deleted" event.
     */
    public function forceDeleted(Initiative $initiative): void
    {
        //
    }
}
