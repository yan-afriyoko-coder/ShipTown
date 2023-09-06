<?php

namespace App\Modules\Webhooks\src\Jobs;

use App\Abstracts\UniqueJob;
use App\Modules\Webhooks\src\Models\PendingWebhook;

/**
 * Class PublishOrdersWebhooksJob.
 */
class ClearOldWebhookRecordsJob extends UniqueJob
{
    public function handle()
    {
        do {
            $recordsUpdated = PendingWebhook::query()
                ->where('created_at', '<', now()->subDays(7))
                ->whereNotNull('published_at')
                ->limit(10000)
                ->forceDelete();
            sleep(1);
        } while ($recordsUpdated > 0);
    }
}
