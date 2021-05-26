<?php

namespace App\Modules\Maintenance\src\Jobs\Products;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class FixQuantityAvailableJob implements ShouldQueue
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
        $invalidProducts = Product::query()
            ->where(
                DB::raw(DB::getTablePrefix() .'products.quantity - '. DB::getTablePrefix() .'products.quantity_reserved'),
                '!=',
                DB::raw(DB::getTablePrefix() .'products.quantity_available')
            )
            ->get();

        $invalidProducts->each(function (Product $product) {
            // calling save method will enforce quantity_available recalculation
            $product->save();
        });

        info('FixQuantityAvailableJob finished successfully', [
            'records_corrected' => $invalidProducts->count()
        ]);
    }
}
