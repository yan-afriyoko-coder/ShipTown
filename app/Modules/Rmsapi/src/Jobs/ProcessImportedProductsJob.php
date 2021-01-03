<?php

namespace App\Modules\Rmsapi\src\Jobs;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductAlias;
use App\Models\RmsapiConnection;
use App\Models\RmsapiProductImport;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Log;
use Ramsey\Uuid\Uuid;

class ProcessImportedProductsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $batch_uuid = null;
    /**
     * Create a new job instance.
     *
     * @param Uuid|null $batch_uuid
     */
    public function __construct(Uuid $batch_uuid = null)
    {
        $this->batch_uuid = $batch_uuid;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $imports = RmsapiProductImport::query()
            ->when(isset($this->batch_uuid), function ($query) {
                return $query->where('batch_uuid', '=', $this->batch_uuid);
            })
            ->whereNull('when_processed')
            ->orderBy('id', 'asc')
            ->get();

        foreach ($imports as $importedProduct) {
            $this->import($importedProduct);
        }
    }

    /**
     * @param RmsapiProductImport $importedProduct
     */
    private function import(RmsapiProductImport $importedProduct): void
    {
        $attributes = [
            "sku" => $importedProduct->raw_import['item_code'],
            "name" => $importedProduct->raw_import['description']
        ];

        $product = Product::updateOrCreate([
            "sku" => $attributes['sku']
        ], $attributes);


        $this->attachTags($importedProduct, $product);

        $this->importAliases($importedProduct, $product);

        $this->importInventory($importedProduct, $product);

        $importedProduct->update([
            'when_processed' => now(),
            'product_id' => $product->id,
            'sku' => $attributes['sku']
        ]);
    }

    /**
     * @param RmsapiProductImport $importedProduct
     * @param Product $product
     */
    private function importAliases(RmsapiProductImport $importedProduct, Product $product): void
    {
        if (! Arr::has($importedProduct->raw_import, 'aliases')) {
            return;
        }

        foreach ($importedProduct->raw_import['aliases'] as $alias) {
            ProductAlias::query()->updateOrCreate(
                array('alias'       => $alias['alias']),
                array('product_id'  => $product->id)
            );
        }
    }

    /**
     * @param RmsapiProductImport $importedProduct
     * @param Product $product
     */
    private function importInventory(RmsapiProductImport $importedProduct, Product $product): void
    {
        $connection = RmsapiConnection::query()->find($importedProduct->connection_id);

        $inventory = Inventory::query()->updateOrCreate([
            'product_id' => $product->id,
            'location_id' => $connection->location_id
        ], [
            'quantity' => $importedProduct->raw_import['quantity_on_hand'],
            'quantity_reserved' => $importedProduct->raw_import['quantity_committed'],
            'shelve_location' => Arr::get($importedProduct->raw_import, 'rmsmobile_shelve_location'),
        ]);
    }

    /**
     * @param RmsapiProductImport $importedProduct
     * @param $product
     */
    private function attachTags(RmsapiProductImport $importedProduct, $product): void
    {
        if ($importedProduct->raw_import['is_web_item']) {
            try {
                $product->attachTag('Available Online');
            } catch (Exception $exception) {
                Log::warning('Could not attach Available Online tag');
            }
        }
    }
}
