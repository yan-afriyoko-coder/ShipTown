<?php

namespace App\Modules\Api2cart\src\Jobs;

use App\Models\Order;
use App\Modules\Api2cart\src\Api\Orders;
use App\Modules\Api2cart\src\Exceptions\RequestException;
use App\Modules\Api2cart\src\Models\Api2cartOrderImports;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class SyncProductJob.
 */
class SyncOrderStatus implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private Order $order;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public int $tries = 20;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public int $retryAfter = 30;

    /**
     * Create a new job instance.
     *
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @throws GuzzleException|RequestException
     */
    public function handle(): bool
    {
        $this->queueData([
            'order_number' => $this->order->order_number,
            'status_code' => $this->order->status_code,
        ]);

        $orderImport = Api2cartOrderImports::where(['order_number' => $this->order->order_number])
            ->latest()
            ->first();

        if ($orderImport === null) {
            return true;
        }

        if ($orderImport->api2cart_order_id === 0) {
            // this will refill api2cart_order_id field
            $orderImport->save();
        }

        $response = Orders::update($orderImport->api2cartConnection->bridge_api_key, [
            'order_id' => $orderImport->api2cart_order_id,
            'order_status' => $this->order->status_code
        ]);

        return $response->isSuccess();
    }
}
