<?php

namespace App\Jobs\Api2cart;

use App\Models\Api2cartOrderImports;
use App\Models\Product;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ImportProductsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var bool
     */
    public $finishedSuccessfully;

    private $orderImport = null;

    /**
     * Create a new job instance.
     *
     */
    public function __construct(Api2cartOrderImports $orderImport)
    {
        $this->finishedSuccessfully = false;
        info('Job Api2cart\ImportProducts dispatched');
        $this->orderImport = $orderImport;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        $orderProducts = collect($this->orderImport['raw_import']['order_products']);
        $productSKUs = $orderProducts->map(function ($item) {
            return collect($item)->only(['name', 'model']);
        });

        foreach ($productSKUs as $product) {
            $x = Product::firstOrCreate([
                'name' => $product['name'],
                'sku' => $product['model']
            ]);
        }

        // finalize
        $this->finishedSuccessfully = true;
    }
}
