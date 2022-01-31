<?php

namespace App\Modules\Api2cart\src\Jobs;

use App\Modules\Api2cart\src\Models\Api2cartOrderImports;
use App\Services\OrderService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessApi2cartImportedOrderJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private ?Api2cartOrderImports $orderImport;

    /**
     * Create a new job instance.
     *
     * @param Api2cartOrderImports $orderImport
     */
    public function __construct(Api2cartOrderImports $orderImport)
    {
        $this->orderImport = $orderImport;
    }

    /**
     * Execute the job.
     *
     * @throws Exception
     *
     * @return void
     */
    public function handle()
    {
        $orderAttributes = [
            'order_number'          => $this->orderImport->raw_import['id'],
            'total_shipping'        => $this->orderImport->raw_import['totals']['shipping'] ?? 0,
            'total'                 => $this->orderImport->raw_import['total']['total'] ?? 0,
            'total_paid'            => $this->orderImport->raw_import['total']['total_paid'] ?? 0,
            'shipping_method_code'  => $this->orderImport->shipping_method_code,
            'shipping_method_name'  => $this->orderImport->shipping_method_name,
            'order_placed_at'       => $this->orderImport->ordersCreateAt(),
            'order_products'        => $this->orderImport->extractOrderProducts(),
            'shipping_address'      => $this->orderImport->extractShippingAddressAttributes(),
            'raw_import'            => $this->orderImport->raw_import,
        ];

        $order = OrderService::updateOrCreate($orderAttributes);

        $this->orderImport->update([
            'order_number'   => $order->order_number,
            'when_processed' => now(),
        ]);
    }
}
