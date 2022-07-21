<?php

namespace App\Modules\Webhooks\src\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use romanzipp\QueueMonitor\Traits\IsMonitored;

/**
 * Class PublishOrdersWebhooksJob.
 */
class PublishAllWebhooksJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use IsMonitored;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        PublishProductsWebhooksJob::dispatch();
        PublishInventoryMovementWebhooksJob::dispatch();
        PublishInventoryWebhooksJob::dispatch();
        PublishOrderProductShipmentWebhooksJob::dispatch();
        PublishOrdersWebhooksJob::dispatch();
        PublishProductsWebhooksJob::dispatch();
    }
}
