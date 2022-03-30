<?php

namespace App\Observers;

use App\Events\Product\ProductCreatedEvent;
use App\Events\Product\ProductUpdatedEvent;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductAlias;
use App\Models\ProductPrice;
use App\Models\Warehouse;
use Exception;

class ProductObserver
{
    /**
     * Handle the product "created" event.
     *
     * @param Product $product
     *
     * @return void
     */
    public function created(Product $product)
    {
        $this->insertInventoryRecords($product);
        $this->insertPricingRecords($product);
        $this->upsertProductAliasRecords($product);

        ProductCreatedEvent::dispatch($product);
    }

    /**
     * Handle the product "updated" event.
     *
     * @param Product $product
     *
     * @throws Exception
     *
     * @return void
     */
    public function updated(Product $product)
    {
        $this->upsertProductAliasRecords($product);

        ProductUpdatedEvent::dispatch($product);
    }

    /**
     * @param Product $product
     */
    private function insertInventoryRecords(Product $product): void
    {
        $warehouse_ids = Warehouse::all(['id','code'])
            ->map(function (Warehouse $warehouse) use ($product) {
                return [
                    'warehouse_id'    => $warehouse->getKey(),
                    'product_id'      => $product->getKey(),
                    'location_id'     => $warehouse->code,
                    'warehouse_code'  => $warehouse->code,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ];
            });

        Inventory::query()->insert($warehouse_ids->toArray());
    }

    /**
     * @param Product $product
     */
    private function insertPricingRecords(Product $product): void
    {
        $productPriceRecords = Warehouse::all(['id','code'])
            ->map(function (Warehouse $warehouse) use ($product) {
                return [
                    'product_id'     => $product->getKey(),
                    'warehouse_id'   => $warehouse->id,
                    'location_id'    => $warehouse->code,
                    'warehouse_code' => $warehouse->code,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ];
            });

        ProductPrice::query()->insert($productPriceRecords->toArray());
    }

    /**
     * @param Product $product
     */
    private function upsertProductAliasRecords(Product $product): void
    {
        ProductAlias::updateOrCreate(
            ['alias' => $product->sku],
            ['product_id' => $product->id]
        );
    }
}
