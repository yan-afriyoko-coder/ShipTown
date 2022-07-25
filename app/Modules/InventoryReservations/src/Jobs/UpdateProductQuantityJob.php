<?php

namespace App\Modules\InventoryReservations\src\Jobs;

use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateProductQuantityJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private int $product_id;

    public function __construct(int $product_id)
    {
        $this->product_id = $product_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $newQuantity = Inventory::query()
            ->where(['product_id' => $this->product_id])
            ->sum('quantity');

        $product = Product::query()
            ->where(['id' => $this->product_id])
            ->where('quantity', '!=', $newQuantity)
            ->get();

        $product->each(function (Product $product) use ($newQuantity) {
            $product->update(['quantity' => $newQuantity]);
        });
    }
}
