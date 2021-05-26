<?php

namespace App\Modules\Maintenance\src\Jobs\Products;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class FixQuantityAvailableJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::debug('Starting FixQuantityAvailableJob');

        $invalidProducts = Product::query()
            ->where(
                DB::raw(DB::getTablePrefix() .'products.quantity - '. DB::getTablePrefix() .'products.quantity_reserved'),
                '!=',
                DB::raw(DB::getTablePrefix() .'products.quantity_available')
            )
            ->get()
            ->each(function (Product $product) {
                // calling save method will recalculate
                $product->quantity_available = $product->quantity - $product->quantity_reserved;
                $product->save();
            });

        info('Finished FixQuantityAvailableJob', ['records_corrected' => $invalidProducts->count()]);
    }
}
