<?php

namespace App\Observers;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\RmsapiConnection;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use Illuminate\Support\Arr;

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
        $rmsapi_locations_ids = RmsapiConnection::all('location_id')
            ->map(function ($connection) use ($product){
                return [
                    'product_id' => $product->getKey(),
                    'location_id' => $connection->location_id
                ];
            });

        $api2cart_location_ids = Api2cartConnection::all('location_id')
            ->map(function ($connection) use ($product){
                return [
                    'product_id' => $product->getKey(),
                    'location_id' => $connection->location_id
                ];
            });

        $insert_data = $rmsapi_locations_ids->merge($api2cart_location_ids);

        Inventory::query()->insert($insert_data->toArray());
    }

    /**
     * Handle the product "updated" event.
     *
     * @param Product $product
     * @return void
     */
    public function updated(Product $product)
    {
        //
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
