<?php

namespace App\Modules\StocktakeSuggestions\src\Listeners;

use App\Modules\StocktakeSuggestions\src\Jobs\OutdatedCountsJob;

class EveryMinuteEventListener
{
    public function handle(): void
    {
        OutdatedCountsJob::dispatch();
    }
}
