<?php

namespace App\Modules\InventoryReservations\src\Jobs;

use App\Models\Inventory;
use App\Models\OrderProduct;
use App\Models\Warehouse;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateInventoryQuantityReservedJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private int $product_id;

    public function __construct(int $product_id)
    {
        $this->product_id = $product_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $newQuantityReserved = OrderProduct::where(['product_id' => $this->product_id])
            ->whereHas('order', function ($query) {
                $query->select(['id'])->where(['is_active' => true]);
            })
            ->sum('quantity_to_ship');

        Inventory::query()
            ->where(['product_id' => $this->product_id])
            ->where(['warehouse_code' => '999'])
            ->where('quantity_reserved', '!=', $newQuantityReserved)
            ->get()
            ->each(function (Inventory $inventory) use ($newQuantityReserved) {
                $inventory->update(['quantity_reserved' => $newQuantityReserved]);
            });
    }
}
