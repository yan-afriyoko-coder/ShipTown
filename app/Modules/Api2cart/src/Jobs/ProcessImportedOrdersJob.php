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
        $data = $orderImport->raw_import;

        $uuid = implode('-', ['module-api2cart-connection-id', $orderImport->connection_id, 'remote-record-id', data_get($data, 'order_id')]);

        $orderAttributes = [
            'custom_unique_reference_id' => $uuid,
            'order_number'               => data_get($data, 'id'),
            'total'                      => data_get($data, 'totals.total', 0),
            'total_products'             => data_get($data, 'totals.subtotal'),
            'total_shipping'             => data_get($data, 'totals.shipping', 0),
            'total_discounts'            => data_get($data, 'totals.discount', 0),
            'total_paid'                 => data_get($data, 'total.total_paid', 0),
            'shipping_method_name'       => data_get($data, 'shipping_method.name', 0),
            'shipping_method_code'       => data_get($data, 'shipping_method.additional_fields.code', 0),
            'order_placed_at'            => $orderImport->ordersCreateAt()->tz('UTC'),
            'order_products'             => $orderImport->extractOrderProducts(),
            'shipping_address'           => $orderImport->extractShippingAddressAttributes(),
            'raw_import'                 => $data,
        ];

        $order = OrderService::updateOrCreate($orderAttributes);

        $orderImport->update([
            'order_number' => $order->order_number,
            'when_processed' => now(),
        ]);
    }
}
