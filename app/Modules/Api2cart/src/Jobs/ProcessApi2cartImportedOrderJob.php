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
use Illuminate\Support\Arr;

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
            'total'                 => $this->orderImport->raw_import['total']['total'] ?? 0,
            'total_paid'            => $this->orderImport->raw_import['total']['total_paid'] ?? 0,
            'order_products'        => $this->orderImport->extractOrderProducts(),
            'shipping_address'      => $this->orderImport->extractShippingAddressAttributes(),
            'raw_import'            => $this->orderImport->raw_import,
        ];

        if ($orderAttributes['status_code'] === 'processing') {
            $orderAttributes = Arr::except($orderAttributes, ['status_code']);
        }

        $order = OrderService::updateOrCreate($orderAttributes);

        $this->orderImport->update([
            'order_number' => $order->order_number,
            'when_processed' => now(),
        ]);
    }
}
