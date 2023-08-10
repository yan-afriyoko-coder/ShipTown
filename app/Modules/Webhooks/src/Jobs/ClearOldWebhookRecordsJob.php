<?php

namespace App\Modules\Webhooks\src\Jobs;

use App\Modules\Webhooks\src\Models\PendingWebhook;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class PublishOrdersWebhooksJob.
 */
class ClearOldWebhookRecordsJob implements ShouldQueue, ShouldBeUnique
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
