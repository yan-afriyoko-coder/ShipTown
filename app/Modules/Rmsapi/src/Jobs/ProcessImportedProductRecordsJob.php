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
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
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

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        $maxRunCount = 3;

        do {
            $this->processImportedProducts(200);
            $maxRunCount--;
        } while ($maxRunCount > 0 and RmsapiProductImport::query()->whereNull('when_processed')->exists());
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

        if ($inventory->quantity !== floatval($ip->raw_import['quantity_on_hand'])) {
            $quantityDelta = $ip->raw_import['quantity_on_hand'] - $inventory->quantity;
            InventoryService::adjustQuantity($inventory, $quantityDelta, 'rms_adjustment');
        }

        if ($inventory->quantity_reserved !== Arr::get($ip->raw_import, 'quantity_committed', 0)
            or $inventory->shelve_location !== Arr::get($ip->raw_import, 'shelve_location', '')
            or $inventory->reorder_point !== Arr::get($ip->raw_import, 'reorder_point', 0)
            or $inventory->restock_level !== Arr::get($ip->raw_import, 'restock_level', 0)
        ) {
            $inventory->update([
                'quantity_reserved' => Arr::get($ip->raw_import, 'quantity_committed', 0),
                'shelve_location'   => Arr::get($ip->raw_import, 'rmsmobile_shelve_location', ''),
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
            or $p->sale_price !== $importedProduct->raw_import['sale_price']
            or $p->sale_price_start_date !== $importedProduct->raw_import['sale_price_start_date'] ?? '2000-01-01'
            or $p->sale_price_end_date !== $importedProduct->raw_import['sale_price_start_date'] ?? '2000-01-01'
        ) {
            $p->update([
                'price'                 => $importedProduct->raw_import['price'],
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
        if ($importedProduct->raw_import['is_web_item']) {
            $product->attachTag('Available Online');
        } else {
            $product->detachTag('Available Online');
        }

        if ($importedProduct->raw_import['department_name']) {
            $product->attachTag($importedProduct->raw_import['department_name']);
        }

        if ($importedProduct->raw_import['category_name']) {
            $product->attachTag($importedProduct->raw_import['category_name']);
        }

        if ($importedProduct->raw_import['supplier_name']) {
            $product->attachTag(trim($importedProduct->raw_import['supplier_name']));
        }

        if ($importedProduct->raw_import['sub_description_1']) {
            $product->attachTag(trim($importedProduct->raw_import['sub_description_1'], 'sub_description_1'));
        }

        if ($importedProduct->raw_import['sub_description_2']) {
            $product->attachTag(trim($importedProduct->raw_import['sub_description_2'], 'sub_description_2'));
        }

        if ($importedProduct->raw_import['sub_description_3']) {
            $product->attachTag(trim($importedProduct->raw_import['sub_description_3'], 'sub_description_3'));
        }
    }

    private function processImportedProducts(int $batch_size): void
    {
        $reservationTime = now();

        RmsapiProductImport::query()
            ->leftJoin('inventory', function (JoinClause $join) {
                $join->on('inventory.product_id', '=', 'rmsapi_product_imports.product_id')
                    ->on('inventory.warehouse_id', '=', 'rmsapi_product_imports.warehouse_id');
            })
            ->whereNull('modules_rmsapi_products_imports.when_processed')
            ->whereNull('modules_rmsapi_products_imports.reserved_at')
            ->where('modules_rmsapi_products_imports.connection_id', $this->connection_id)
            ->orderByRaw('(inventory.quantity != modules_rmsapi_products_imports.quantity_on_hand) DESC, ' .
                'modules_rmsapi_products_imports.id ASC')
            ->limit($batch_size)
            ->update(['modules_rmsapi_products_imports.reserved_at' => $reservationTime]);

        $records = RmsapiProductImport::query()
            ->whereNull('when_processed')
            ->where(['reserved_at' => $reservationTime])
            ->where('connection_id', $this->connection_id)
            ->orderBy('id')
            ->get();

        $records->each(function (RmsapiProductImport $productImport) {
            try {
                retry(3, function () use ($productImport) {
                    $this->import($productImport);
                }, 100);
            } catch (Exception $e) {
                report($e);
            }
        });
    }
}
