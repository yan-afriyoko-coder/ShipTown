<?php

namespace App\Modules\StocktakeSuggestions\src\Listeners;

use App\Models\StocktakeSuggestion;

class DailyEventListener
{
    public function handle()
    {
        StocktakeSuggestion::query()->delete();
    }
}
