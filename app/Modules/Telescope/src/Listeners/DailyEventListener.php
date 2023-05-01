<?php

namespace App\Modules\Telescope\src\Listeners;

use App\Events\DailyEvent;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class DailyEventListener
{
    public function handle(DailyEvent $event): bool
    {
        Artisan::call('telescope:prune', ['--hours' => config('telescope.hours')]);

        Log::info('Telescope pruned');

        return true;
    }
}
