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

class ProcessImportedOrdersJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Execute the job.
     *
     * @throws Exception
     *
     * @return void
     */
    public function handle()
    {
        Api2cartOrderImports::query()
            ->whereNull('when_processed')
            ->orderBy('id')
            ->chunk(1, function ($api2cartOrderImports) {
                foreach ($api2cartOrderImports as $api2cartOrderImport) {
                    $this->processOrder($api2cartOrderImport);
                }
            });
    }

    /**
     * @throws Exception
     */
    private function processOrder(Api2cartOrderImports $orderImport): void
    {
        $orderAttributes = [
            'order_number' => $orderImport->raw_import['id'],
            'total_shipping' => $orderImport->raw_import['totals']['shipping'] ?? 0,
            'total_discounts' => $orderImport->raw_import['totals']['discount'] ?? 0,
            'total' => $orderImport->raw_import['total']['total'] ?? 0,
            'total_paid' => $orderImport->raw_import['total']['total_paid'] ?? 0,
            'shipping_method_code' => $orderImport->shipping_method_code,
            'shipping_method_name' => $orderImport->shipping_method_name,
            'order_placed_at' => $orderImport->ordersCreateAt(),
            'order_products' => $orderImport->extractOrderProducts(),
            'shipping_address' => $orderImport->extractShippingAddressAttributes(),
            'raw_import' => $orderImport->raw_import,
        ];

        $order = OrderService::updateOrCreate($orderAttributes);

        $orderImport->update([
            'order_number' => $order->order_number,
            'when_processed' => now(),
        ]);
    }
}
