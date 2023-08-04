<?php

namespace App\Modules\Webhooks\src\Listeners;

use App\Modules\Webhooks\src\Models\PendingWebhook;

class DailyEventListener
{
    public function handle()
    {
        do {
            $recordsUpdated =  PendingWebhook::query()
                ->whereNotNull('published_at')
                ->where('created_at', '>', now()->subDay())
                ->limit(1000)
                ->update(['published_at' => null, 'reserved_at' => null]);
            sleep(1);
        } while ($recordsUpdated > 0);

        do {
            $recordsUpdated = PendingWebhook::query()
                ->where('created_at', '<', now()->subDays(7))
                ->whereNotNull('published_at')
                ->limit(1000)
                ->forceDelete();
            sleep(1);
        } while ($recordsUpdated > 0);
    }
}
