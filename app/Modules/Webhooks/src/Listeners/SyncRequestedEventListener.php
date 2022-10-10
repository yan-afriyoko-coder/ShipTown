<?php

namespace App\Modules\Webhooks\src\Listeners;

use App\Modules\Webhooks\src\Jobs\PublishAllWebhooksJob;

class SyncRequestedEventListener
{
    public function handle()
    {
        PublishAllWebhooksJob::dispatch();
    }
}
