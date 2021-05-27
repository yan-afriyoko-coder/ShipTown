<?php

namespace App\Modules\AutoTags\src\Jobs;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class AddMissingOversoldTagsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::debug('Starting RemoveWrongOutOfStockTagsJob');

        $invalidProducts = Product::withoutAllTags(['oversold'])
            ->where('quantity_available', '<', 0)
            ->get()
            ->each(function (Product $product) {
                $product->attachTag('oversold');
            });

        info('Finished RemoveWrongOutOfStockTagsJob', ['records_corrected' => $invalidProducts->count()]);
    }
}
