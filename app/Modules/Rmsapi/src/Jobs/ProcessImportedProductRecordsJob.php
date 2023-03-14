<?php

namespace App\Modules\Rmsapi\src\Jobs;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductAlias;
use App\Models\ProductPrice;
use App\Modules\Rmsapi\src\Models\RmsapiConnection;
use App\Modules\Rmsapi\src\Models\RmsapiProductImport;
use App\Services\InventoryService;
use App\Traits\IsMonitored;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessImportedProductRecordsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use IsMonitored;

    private int $connection_id;

    public function __construct(int $connection_id)
    {
        $this->connection_id = $connection_id;
    }

    public function middleware(): array
    {
        return [(new WithoutOverlapping($this->connection_id))->dontRelease()];
    }

    public function handle()
    {
        $batch_size = 200;
        $maxRunCount = 5;

        do {
            $this->processImportedProducts($batch_size);

            $hasNoRecordsToProcess = ! RmsapiProductImport::query()
                ->whereNull('reserved_at')
                ->whereNull('when_processed')
                ->exists();

            if ($hasNoRecordsToProcess) {
                return true;
            }

            $maxRunCount--;
        } while ($maxRunCount > 0);
    }

    /**
     * @param RmsapiProductImport $importedProduct
     */
    private function import(RmsapiProductImport $importedProduct): void
    {
        $attributes = [
            'sku'  => $importedProduct->raw_import['item_code'],
            'name' => $importedProduct->raw_import['description'],
        ];

        /** @var Product $product */
        $product = Product::firstOrCreate(['sku' => $attributes['sku']], $attributes);

        if ($product->name !== $attributes['name']) {
            Log::debug('RMSAPI updating product name', [
                'sku' => $product->sku,
                'old_name' => $product->name,
                'new_name' => $attributes['name'],
            ]);
            $product->update(['name' => $attributes['name']]);
        }

        $this->attachTags($importedProduct, $product);

        $this->importAliases($importedProduct, $product);

        $this->importInventory($importedProduct, $product);

        $this->importPricing($importedProduct, $product);

        $importedProduct->update([
            'when_processed' => now(),
            'product_id'     => $product->id,
            'sku'            => $attributes['sku'],
        ]);
    }

    /**
     * @param RmsapiProductImport $importedProduct
     * @param Product             $product
     */
    private function importAliases(RmsapiProductImport $importedProduct, Product $product): void
    {
        if (!Arr::has($importedProduct->raw_import, 'aliases')) {
            return;
        }

        foreach ($importedProduct->raw_import['aliases'] as $alias) {
            $productAlias = ProductAlias::query()
                ->firstOrCreate([
                    'alias'      => $alias['alias'],
                ], [
                    'product_id' => $product->id,
                ]);

            if ($productAlias->product_id !== $product->id) {
                $productAlias->update(['product_id' => $product->id]);
            }
        }
    }

    /**
     * @param RmsapiProductImport $ip
     * @param Product             $product
     */
    private function importInventory(RmsapiProductImport $ip, Product $product): void
    {
        $connection = RmsapiConnection::query()->find($ip->connection_id);

        $inventory = Inventory::query()
            ->where([
                'product_id' => $product->id,
                'warehouse_id' => $connection->warehouse_id,
            ])
            ->first();

        if ($inventory->quantity_reserved !== Arr::get($ip->raw_import, 'quantity_committed', 0)
            or $inventory->reorder_point !== Arr::get($ip->raw_import, 'reorder_point', 0)
            or $inventory->restock_level !== Arr::get($ip->raw_import, 'restock_level', 0)
        ) {
            $inventory->update([
                'quantity_reserved' => Arr::get($ip->raw_import, 'quantity_committed', 0),
                'reorder_point'     => Arr::get($ip->raw_import, 'reorder_point', 0),
                'restock_level'     => Arr::get($ip->raw_import, 'restock_level', 0),
            ]);
        }
    }

    /**
     * @param RmsapiProductImport $importedProduct
     * @param Product             $product
     */
    private function importPricing(RmsapiProductImport $importedProduct, Product $product): void
    {
        $connection = RmsapiConnection::query()->find($importedProduct->connection_id);

        $p = ProductPrice::query()
            ->where([
                'product_id' => $product->id,
                'warehouse_id' => $connection->warehouse_id,
            ])
            ->first();

        if ($p->price !== $importedProduct->raw_import['price']
            or $p->cost !== $importedProduct->raw_import['cost']
            or $p->sale_price !== $importedProduct->raw_import['sale_price']
            or $p->sale_price_start_date !== $importedProduct->raw_import['sale_price_start_date'] ?? '2000-01-01'
            or $p->sale_price_end_date !== $importedProduct->raw_import['sale_price_start_date'] ?? '2000-01-01'
        ) {
            $p->update([
                'price'                 => $importedProduct->raw_import['price'],
                'cost'                  => $importedProduct->raw_import['cost'],
                'sale_price'            => $importedProduct->raw_import['sale_price'],
                'sale_price_start_date' => $importedProduct->raw_import['sale_start_date'] ?? '2000-01-01',
                'sale_price_end_date'   => $importedProduct->raw_import['sale_end_date'] ?? '2000-01-01',
            ]);
        }
    }

    /**
     * @param RmsapiProductImport $importedProduct
     * @param Product             $product
     */
    private function attachTags(RmsapiProductImport $importedProduct, Product $product): void
    {
        $product->tags()->detach();

        if ($importedProduct->raw_import['is_web_item']) {
            $product->attachTag('Available Online');
        } else {
            $product->detachTag('Available Online');
        }

        if ($importedProduct->raw_import['department_name']) {
            $product->syncTagByType('rms_department_name', trim($importedProduct->raw_import['department_name']));
        }

        if ($importedProduct->raw_import['category_name']) {
            $product->syncTagByType('rms_category_name', trim($importedProduct->raw_import['category_name']));
        }

        if ($importedProduct->raw_import['supplier_name']) {
            $product->syncTagByType('rms_supplier_name', trim($importedProduct->raw_import['supplier_name']));
        }

        if ($importedProduct->raw_import['sub_description_1']) {
            $product->syncTagByType('rms_sub_description_1', trim($importedProduct->raw_import['sub_description_1']));
        }

        if ($importedProduct->raw_import['sub_description_2']) {
            $product->syncTagByType('rms_sub_description_2', trim($importedProduct->raw_import['sub_description_2']));
        }

        if ($importedProduct->raw_import['sub_description_3']) {
            $product->syncTagByType('rms_sub_description_3', trim($importedProduct->raw_import['sub_description_3']));
        }
    }

    private function processImportedProducts(int $batch_size): void
    {
        $reservationTime = now();

        RmsapiProductImport::query()
            ->whereNull('reserved_at')
            ->where('connection_id', $this->connection_id)
            ->whereNull('when_processed')
            ->orderByRaw('id ASC')
            ->limit($batch_size)
            ->update(['reserved_at' => $reservationTime]);

        $records = RmsapiProductImport::query()
            ->where('connection_id', $this->connection_id)
            ->where(['reserved_at' => $reservationTime])
            ->whereNull('when_processed')
            ->orderBy('id')
            ->get();

        $records->each(function (RmsapiProductImport $productImport) {
            try {
                retry(3, function () use ($productImport) {
                    DB::transaction(function () use ($productImport) {
                        $this->import($productImport);
                    });
                }, 1000);
            } catch (Exception $e) {
                report($e);
                Log::emergency($e->getMessage(), $e->getTrace());
            }
        });
    }
}
