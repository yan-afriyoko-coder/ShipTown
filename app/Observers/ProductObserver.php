<?php

namespace App\Observers;

use App\Events\Product\CreatedEvent;
use App\Events\Product\UpdatedEvent;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\RmsapiConnection;
use App\Models\Warehouse;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use App\Modules\Api2cart\src\Products;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class ProductObserver
{
    /**
     * Handle the product "created" event.
     *
     * @param Product $product
     * @return void
     */
    public function created(Product $product)
    {
        $warehouse_ids = Warehouse::all('id')
            ->map(function ($warehouse) use ($product) {
                return [
                    'warehouse_id' => $warehouse->getKey(),
                    'product_id' => $product->getKey(),
                    'location_id' => $warehouse->getKey(),
                ];
            });

        Inventory::query()->insert($warehouse_ids->toArray());

        CreatedEvent::dispatch($product);
    }

    /**
     * Handle the product "updated" event.
     *
     * @param Product $product
     * @return void
     * @throws Exception
     */
    public function updated(Product $product)
    {
        UpdatedEvent::dispatch($product);

        try {
            if ($product->quantity_available <= 0) {
                $connection = Api2cartConnection::query()->first();
                if ($connection) {
                    $product_data = [
                        'product_id' => $product->getKey(),
                        'sku' => $product->sku,
                        'quantity' => 0,
                        'in_stock' => false,
                    ];
                    Log::debug('Disabling product', $product->toArray());
                    Products::update($connection->bridge_api_key, $product_data);
                }
            }
        } catch (Exception $exception) {
            Log::warning('Could not disable product', $product);
        }
    }

    /**
     * Handle the product "deleted" event.
     *
     * @param Product $product
     * @return void
     */
    public function deleted(Product $product)
    {
        //
    }

    /**
     * Handle the product "restored" event.
     *
     * @param Product $product
     * @return void
     */
    public function restored(Product $product)
    {
        //
    }

    /**
     * Handle the product "force deleted" event.
     *
     * @param Product $product
     * @return void
     */
    public function forceDeleted(Product $product)
    {
        //
    }
}
