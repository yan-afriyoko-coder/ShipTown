<?php

namespace App\Jobs\Rmsapi;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\RmsapiProductImport;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Arr;

class ProcessImporedProductsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $imports = RmsapiProductImport::query()
            ->whereNull('when_processed')
            ->orderBy('id')
            ->get();

        foreach ($imports as $importedProduct) {

            $attributes = [
                "sku" => $importedProduct->raw_import['item_code'],
                "name" => $importedProduct->raw_import['description']
            ];

            $product = Product::query()->updateOrCreate([
                "sku" => $attributes['sku']
            ], $attributes);

            dd($product);

            Inventory::query()->updateOrCreate();

            $importedProduct->update([
                'when_processed' => now()
            ]);

        }
    }
}
