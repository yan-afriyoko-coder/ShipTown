<?php

namespace App\Jobs\Api2cart;

use App\Events\OrderCreatedEvent;
use App\Events\OrderStatusChangedEvent;
use App\Models\OrderAddress;
use App\Modules\Api2cart\src\Models\Api2cartOrderImports;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderProduct;
use App\Models\OrderProductOption;
use App\Services\OrderService;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use test\Mockery\HasUnknownClassAsTypeHintOnMethod;

class ProcessApi2cartImportedOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $orderImport = null;

    /**
     * Create a new job instance.
     * @param Api2cartOrderImports $orderImport
     */
    public function __construct(Api2cartOrderImports $orderImport)
    {
        $this->orderImport = $orderImport;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        $orderAttributes = [
            'order_number'          => $this->orderImport->raw_import['id'],
            'status_code'           => $this->orderImport->raw_import['status']['id'],
            'order_products'        => $this->orderImport->extractOrderProducts(),
            'shipping_address'      => $this->orderImport->extractShippingAddressAttributes(),
            'raw_import'            => $this->orderImport->raw_import,
        ];

        $order = OrderService::updateOrCreate($orderAttributes);

        $this->orderImport->update([
            'order_number' => $order->order_number,
            'when_processed' => now(),
        ]);
    }

}
