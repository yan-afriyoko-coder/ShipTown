<?php

namespace App\Modules\InventoryReservations\src\Jobs;

use App\Models\Inventory;
use App\Models\OrderProduct;
use App\Models\OrderStatus;
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
        Inventory::whereLocationId(999)
            ->whereNotIn('quantity_reserved', [0])
            ->whereNotIn('product_id', $this->checkedProductIds)
            ->get()
            ->each(function (Inventory $inventory) {
                Log::warning('Incorrect quantity_reserved detected', [
                    'sku'                        => $inventory->product->sku,
                    'quantity_reserved_expected' => 0,
                    'quantity_reserved_current'  => $inventory->quantity_reserved,
                ]);

                $inventory->product->log('Resetting quantity reserved');
                $inventory->quantity_reserved = 0;
                $inventory->save();
            });
    }

    /**
     * @return void
     */
    private function fixReservedQuantity(): void
    {
         OrderProduct::select([
                'product_id',
                DB::raw('sum(quantity_to_ship) as new_quantity_reserved'),
            ])
            ->whereHas('order', function ($query) {
                $query->select(['id'])->whereIn('status_code', OrderStatus::whereReservesStock(true)->select('code'));
            })
             ->whereNotNull('product_id')
             ->whereNotIn('product_id', $this->checkedProductIds)
             ->groupBy('product_id')
             ->get()
             ->each(function ($orderProduct) {
                $this->checkedProductIds[] = $orderProduct->product_id;

                $inventory = Inventory::firstOrCreate([
                    'product_id'  => $orderProduct->product_id,
                    'location_id' => 999,
                ]);

                if ($inventory->quantity_reserved != $orderProduct->getAttribute('new_quantity_reserved')) {
                    Log::warning('Incorrect quantity_reserved detected', [
                        'sku'                        => $inventory->product->sku,
                        'quantity_reserved_expected' => $orderProduct->getAttribute('new_quantity_reserved'),
                        'quantity_reserved_current'  => $inventory->quantity_reserved,
                    ]);
                    $orderProduct->product->log('Recalculated quantity_reserved');
                    $inventory->quantity_reserved = $orderProduct->getAttribute('new_quantity_reserved');
                    $inventory->save();
                }
             });
    }
}
