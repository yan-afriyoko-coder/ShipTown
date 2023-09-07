<?php

namespace App\Modules\Webhooks\src\Listeners;

use App\Modules\Webhooks\src\Jobs\ClearOldWebhookRecordsJob;
use App\Modules\Webhooks\src\Services\WebhooksService;

class SyncRequestedEventListener
{
    public function handle()
    {
        ClearOldWebhookRecordsJob::dispatch();
        WebhooksService::dispatchJobs();
    }
}
