<?php

namespace App\Modules\InventoryReservations\src\Jobs;

use App\Abstracts\UniqueJob;
use App\Models\Inventory;
use App\Models\OrderProduct;
use App\Modules\InventoryReservations\src\Models\Configuration;

class UpdateInventoryQuantityReservedJob extends UniqueJob
{
    private int $product_id;

    public function __construct(int $product_id)
    {
        $this->product_id = $product_id;
    }

    public function handle()
    {
        $newQuantityReserved = OrderProduct::where(['product_id' => $this->product_id])
            ->whereHas('order', function ($query) {
                $query->select(['id'])->where(['is_active' => true]);
            })
            ->sum('quantity_to_ship');

        /** @var Configuration $configuration */
        $configuration = Configuration::query()->firstOrCreate();

        if ($configuration->warehouse_id === null) {
            return;
        }

        Inventory::query()
            ->where(['product_id' => $this->product_id])
            ->where(['warehouse_id' => $configuration->warehouse_id])
            ->where('quantity_reserved', '!=', $newQuantityReserved)
            ->get()
            ->each(function (Inventory $inventory) use ($newQuantityReserved) {
                $inventory->update(['quantity_reserved' => $newQuantityReserved]);
            });
    }
}
