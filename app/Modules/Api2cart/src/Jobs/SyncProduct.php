<?php

namespace App\Modules\Api2cart\src\Jobs;

use App\Modules\Api2cart\src\Exceptions\RequestException;
use App\Modules\Api2cart\src\Models\Api2cartProductLink;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Class SyncProductJob.
 */
class SyncProduct implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var Api2cartProductLink
     */
    private Api2cartProductLink $product_link;

    /**
     * Create a new job instance.
     *
     * @param Api2cartProductLink $api2cartProductLink
     */
    public function __construct(Api2cartProductLink $api2cartProductLink)
    {
        $this->product_link = $api2cartProductLink;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws GuzzleException
     * @throws RequestException
     */
    public function handle()
    {
        if ($this->product_link->postProductUpdate() && $this->verifyProductUpdate()) {
            $product = $this->product_link->product;
            activity()->withoutLogs(function () use ($product) {
                $product->detachTag('Not Synced');
            });

            $product->detachTag('SYNC ERROR');
            return;
        }

        $this->product_link->product->attachTag('SYNC ERROR');
        $this->product_link->product->log('eCommerce: Sync failed, see logs for more details');
    }

    /**
     * @return bool
     * @throws RequestException
     * @throws GuzzleException
     */
    private function verifyProductUpdate(): bool
    {
        if ($this->product_link->isInSync()) {
            $this->product_link->product->detachTag('CHECK FAILED');
            return true;
        }

        $this->product_link->product->attachTag('CHECK FAILED');
        $this->product_link->product->log('eCommerce: Sync check failed, see logs');
        return false;
    }
}
