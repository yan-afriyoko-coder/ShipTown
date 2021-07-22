<?php

namespace App\Modules\Api2cart\src\Jobs;

use App\Models\Order;
use App\Modules\Api2cart\src\Api\Orders;
use App\Modules\Api2cart\src\Exceptions\RequestException;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use App\Modules\Api2cart\src\Models\Api2cartOrderImports;
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
class SyncOrderStatus implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private Order $order;

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
     * @return void
     * @throws GuzzleException
     * @throws RequestException
     */
    public function handle()
    {
        $orderImport = Api2cartOrderImports::where(['order_number' => $this->order->order_number])
            ->latest()
            ->first();

        $api2cartConnection = Api2cartConnection::find($orderImport->connection_id);

        Orders::update($api2cartConnection->bridge_api_key, [
            'order_id' => $orderImport->api2cart_order_id,
            'order_status' => $this->order->status_code
        ]);
    }
}
