<?php

namespace App\Modules\InventoryQuantityIncoming\src\Jobs;

use App\Models\Inventory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 *
 */
class FixIncorrectQuantityIncomingJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public ?int $product_id;
    public ?int $warehouse_id;

    public function __construct(int $product_id = null, int $warehouse_id = null)
    {
        $this->product_id = $product_id;
        $this->warehouse_id = $warehouse_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $query = Inventory::query()
            ->selectRaw('
                inventory.id as id,
                MAX(inventory.product_id) as product_id,
                MAX(inventory.warehouse_id) as warehouse_id,
                MAX(inventory.quantity_incoming) as actual_quantity_incoming,
                SUM(IFNULL(data_collection_records.quantity_requested - data_collection_records.total_transferred_in, 0)) as expected_quantity_incoming
            ')
            ->leftJoin('data_collections', function ($join) {
                $join->on('data_collections.warehouse_id', '=', 'inventory.warehouse_id');
            })
            ->leftJoin('data_collection_records', function ($join) {
                $join->on('data_collection_records.product_id', '=', 'inventory.product_id')
                    ->on('data_collection_records.data_collection_id', '=', 'data_collections.id');
            })
            ->when($this->product_id, function ($query) {
                return $query->where('inventory.product_id', $this->product_id);
            })
            ->when($this->warehouse_id, function ($query) {
                return $query->where('inventory.warehouse_id', $this->warehouse_id);
            })
            ->groupByRaw('inventory.id')
            ->havingRaw('actual_quantity_incoming != expected_quantity_incoming');

        $result = $query->get();

        $result->each(function (Inventory $inventory) {
            $inventory->update(['quantity_incoming' => $inventory->expected_quantity_incoming]);
        });
    }
}
