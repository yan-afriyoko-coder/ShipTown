<?php

namespace App\Modules\InventoryReservations\src\Jobs;

use App\Helpers\TemporaryTable;
use App\Models\Inventory;
use App\Models\OrderProduct;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 *
 */
class RecalculateQuantityJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private string $table_name;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->table_name = 'temp_table_totals_' . rand(100000, 99999999);

        $this->prepareTempTable();

        $this->incorrectInventoryRecordsQuery()
            ->get()
            ->each(function (Product $product) {
                Log::warning('Incorrect quantity detected', [
                    'sku'               => $product->sku,
                    'quantity_expected' => $product['quantity_expected'],
                    'quantity_actual'   => $product['quantity_actual'],
                ]);

                $product->log('Fixing quantity');

                $product->update(['quantity' => $product['quantity_expected']]);
            });
    }

    private function prepareTempTable(): void
    {
        $baseQuery = Inventory::query()
            ->select([
                'inventory.product_id',
                DB::raw('sum(inventory.quantity) as quantity_expected')
            ])
            ->groupBy('inventory.product_id');

        TemporaryTable::create($this->table_name, $baseQuery);
    }

    /**
     * @return Builder|Product
     */
    private function incorrectInventoryRecordsQuery()
    {
        return Product::query()
            ->select([
                'products.id',
                'products.sku',
                'products.quantity as quantity_actual',
                DB::raw('IFNULL('.$this->table_name.'.quantity_expected, 0) as quantity_expected'),
            ])
            ->leftJoin($this->table_name, 'products.id', '=', $this->table_name.'.product_id')
            ->whereRaw('products.quantity != IFNULL('. $this->table_name . '.quantity_expected, 0)');
    }
}
