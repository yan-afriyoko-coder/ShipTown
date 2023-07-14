<?php

namespace App\Modules\StocktakeSuggestions\src\Listeners;

use App\Modules\StocktakeSuggestions\src\Jobs\NoMovementJob;

class EveryDayEventListener
{
    public function handle()
    {
        NoMovementJob::dispatch();
    }
}
