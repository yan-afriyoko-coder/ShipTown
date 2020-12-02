<?php

namespace App\Jobs\Modules\Api2cart;

use App\Models\Order;
use App\Modules\Api2cart\src\Models\Api2cartOrderImports;
use App\Services\OrderService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportShippingAddressJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $order_id;

    /**
     * Create a new job instance.
     *
     * @param int $order_id
     */
    public function __construct(int $order_id)
    {
        $this->order_id = $order_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $order = Order::find($this->order_id);

        if (!$order) {
            return;
        }

        $order_import = Api2cartOrderImports::query()
            ->where('order_number', '=', $order->order_number)
            ->latest()->first();

        if (!$order_import) {
            return;
        }

        OrderService::updateOrCreateShippingAddress($order, $order_import->extractShippingAddressAttributes());
    }
}
