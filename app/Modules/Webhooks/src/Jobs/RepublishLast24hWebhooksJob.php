<?php

namespace App\Modules\Webhooks\src\Jobs;

use App\Abstracts\UniqueJob;
use App\Modules\Webhooks\src\Models\PendingWebhook;

/**
 * Class PublishOrdersWebhooksJob.
 */
class RepublishLast24hWebhooksJob extends UniqueJob
{
    public function handle()
    {
        do {
            $recordsUpdated =  PendingWebhook::query()
                ->whereNotNull('published_at')
                ->where('created_at', '>', now()->subDay())
                ->limit(1000)
                ->update(['published_at' => null, 'reserved_at' => null]);

            usleep(200000); // 200ms
        } while ($recordsUpdated > 0);
    }
}
