<?php

namespace App\Modules\InventoryReservations\src\Jobs;

use App\Models\Inventory;
use App\Models\OrderProduct;
use App\Models\OrderStatus;
use App\Models\Warehouse;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Lcobucci\JWT\Builder;

class UpdateQuantityReservedJob implements ShouldQueue
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
                $query->select(['id'])->whereIn('status_code', OrderStatus::whereReservesStock(true)->select('code'));
            })
            ->sum('quantity_to_ship');

        Warehouse::firstOrCreate(['code' => '999'], ['name' => '999']);

        Inventory::updateOrCreate([
            'product_id'  => $this->product_id,
            'location_id' => 999,
            'warehouse_code' => '999'
        ], [
            'quantity_reserved' => $newQuantityReserved,
        ]);
    }
}
