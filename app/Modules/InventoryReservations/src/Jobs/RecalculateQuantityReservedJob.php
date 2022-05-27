<?php

namespace App\Modules\InventoryReservations\src\Jobs;

use App\Models\Inventory;
use App\Models\OrderProduct;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 *
 */
class RecalculateQuantityReservedJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var array
     */
    private array $checkedProductIds = [];

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->fixReservedQuantity();

        $this->fixNotReserved();
    }

    /**
     * @return void
     */
    private function fixNotReserved(): void
    {
        $query = Inventory::query()
            ->select([
                'inventory.*',
            ])
            ->join('orders_products', function ($join) {
                $join->on('orders_products.product_id', '=', 'inventory.product_id');
            })
            ->leftJoin('orders', function ($join) {
                $join->on('orders_products.order_id', '=', 'orders.id');
                $join->on('orders.is_active', '=', DB::raw(true));
            })
            ->where(['inventory.warehouse_code' => 999])
            ->where('inventory.quantity_reserved', '!=', 0)
            ->whereNull('orders.id')
            ->whereNotIn('inventory.product_id', $this->checkedProductIds);


        $query->get()
            ->each(function (Inventory $inventory) {
                Log::warning('Incorrect quantity_reserved detected', [
                    'sku'                        => $inventory->product->sku,
                    'quantity_reserved_expected' => 0,
                    'quantity_reserved_current'  => $inventory->quantity_reserved,
                ]);

                $inventory->product->log('Resetting quantity reserved');

                $inventory->update(['quantity_reserved' => 0]);
                $inventory->save();
            });
    }

    /**
     * @return void
     */
    private function fixReservedQuantity(): void
    {
        $query = OrderProduct::query()
            ->select([
                'orders_products.product_id',
                DB::raw('999 as warehouse_code'),
                DB::raw('sum(quantity_to_ship) as quantity_reserved_expected'),
                DB::raw('max(inventory.quantity_reserved) as quantity_reserved_actual')
            ])
            ->leftJoin('inventory', function ($join) {
                $join->on('inventory.product_id', '=', 'orders_products.product_id');
                $join->on('inventory.warehouse_code', '=', DB::raw('999'));
            })
            ->whereHas('order', function ($query) {
                $query->select(['id'])->where(['is_active' => true]);
            })
            ->whereNotNull('orders_products.product_id')
            ->whereNotIn('orders_products.product_id', $this->checkedProductIds)
            ->having('quantity_reserved_expected', '!=', DB::raw('max(inventory.quantity_reserved)'))
            ->groupBy('orders_products.product_id');

        $query->get()
             ->each(function ($orderProduct) {
                 $this->checkedProductIds[] = $orderProduct->product_id;

                 $inventory = Inventory::query()
                     ->where([
                         'product_id' => $orderProduct->product_id,
                         'warehouse_code' => '999',
                     ])
                     ->first();

                 $inventory->update([
                        'quantity_reserved'    => $orderProduct->getAttribute('quantity_reserved_expected'),
                    ]);

                Log::warning('Incorrect quantity_reserved detected', [
                    'product_id'                 => $orderProduct->getAttribute('product_id'),
                    'quantity_reserved_expected' => $orderProduct->getAttribute('quantity_reserved_expected'),
                    'quantity_reserved_current'  => $orderProduct->getAttribute('quantity_reserved_expected'),
                ]);
             });
    }
}
