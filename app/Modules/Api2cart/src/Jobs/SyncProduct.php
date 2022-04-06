<?php

namespace App\Modules\Api2cart\src\Jobs;

use App\Modules\Api2cart\src\Models\Api2cartProductLink;
use App\Modules\Api2cart\src\Services\Api2cartService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
     */
    public function handle()
    {
        if ($this->product_link->api2cart_product_type === 'configurable') {
            $this->product_link->product->detachTag('Not Synced');
            $this->product_link->product->detachTag('SYNC ERROR');
            $this->product_link->product->detachTag('CHECK FAILED');
            return;
        }

        $updateSuccess = Api2cartService::updateSku($this->product_link);
        $updateVerified = Api2cartService::verifyIfProductInSync($this->product_link);

        if ($updateSuccess && $updateVerified) {
            $this->product_link->product->detachTag('SYNC ERROR');
            $this->product_link->product->detachTagSilently('Not Synced');
        } else {
            $this->product_link->product->attachTag('SYNC ERROR');
        }
    }
}
