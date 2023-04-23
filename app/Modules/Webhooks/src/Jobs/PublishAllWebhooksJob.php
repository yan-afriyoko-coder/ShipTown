<?php

namespace App\Modules\Webhooks\src\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class PublishOrdersWebhooksJob.
 */
class PublishAllWebhooksJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        PublishInventoryMovementWebhooksJob::dispatch();
        PublishOrderProductShipmentWebhooksJob::dispatch();
        PublishInventoryWebhooksJob::dispatch();
//        PublishOrdersWebhooksJob::dispatch();
    }
}
