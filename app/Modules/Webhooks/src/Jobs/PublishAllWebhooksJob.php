<?php

namespace App\Modules\Webhooks\src\Jobs;

use App\Abstracts\UniqueJob;

/**
 * Class PublishOrdersWebhooksJob.
 */
class PublishAllWebhooksJob extends UniqueJob
{
    public function handle()
    {
        PublishInventoryMovementWebhooksJob::dispatch();
        PublishOrderProductShipmentWebhooksJob::dispatch();
        PublishInventoryWebhooksJob::dispatch();
    }
}
