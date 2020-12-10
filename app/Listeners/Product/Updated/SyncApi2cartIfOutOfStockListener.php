<?php

namespace App\Listeners\Product\Updated;

use App\Events\Product\UpdatedEvent;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use App\Modules\Api2cart\src\Products;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SyncApi2cartIfOutOfStockListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param UpdatedEvent $event
     * @return void
     */
    public function handle(UpdatedEvent $event)
    {
        $product = $event->getProduct();

        if ($product->quantity_available > 0) {
            return;
        }

        try {
            Api2cartConnection::all()
                ->each(function ($connection) use ($product) {
                    $product_data = [
                        'product_id' => $product->getKey(),
                        'sku' => $product->sku,
                        'quantity' => 0,
                        'in_stock' => "False",
                    ];
                    if (Products::update($connection->bridge_api_key, $product_data)->isSuccess()) {
                        $product->log('Product quantity set to 0 on website');
                    }
                });
        } catch (Exception $exception) {
            $product->log('Could not set quantity to 0 on website');
            Log::error('Could not set product quantity to 0 on website', [
                'message' => $exception->getMessage()
            ]);
        }
    }
}
