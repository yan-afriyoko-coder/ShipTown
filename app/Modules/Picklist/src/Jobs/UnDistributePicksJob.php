<?php

namespace App\Modules\Picklist\src\Jobs;

use App\Abstracts\UniqueJob;
use App\Models\OrderProductPick;
use App\Models\Pick;
use Cache;

class UnDistributePicksJob extends UniqueJob
{
    private ?Pick $pick;
    private string $key;

    public function __construct(Pick $pick = null)
    {
        $this->pick = $pick;
        $this->key = implode('_', [get_class($this), data_get($this->pick, 'id', 0)]);
    }

    public function uniqueId(): string
    {
        return $this->key;
    }

    public function handle(): void
    {
        Cache::lock($this->key, 30)->get(function () {
            $this->unDistributePicks();
        });
    }

    /**
     * @return void
     */
    public function unDistributePicks(): void
    {
        Pick::query()
            ->onlyTrashed()
            ->where('quantity_distributed', '>', 0)
            ->each(function (Pick $pick) {
                OrderProductPick::query()
                    ->where('pick_id', $pick->id)
                    ->delete();
            });

        OrderProductPick::query()
            ->with(['orderProduct', 'pick'])
            ->onlyTrashed()
            ->where('quantity_picked', '>', 0)
            ->get()
            ->each(function (OrderProductPick $orderProductPick) {

                ray($orderProductPick);

                ray(Pick::query()->get()->all());

                $orderProductPick->update([
                    'quantity_picked' => 0,
                    'quantity_skipped_picking' => 0,
                ]);

                $orderProductPick->orderProduct->update([
                    'quantity_picked' => OrderProductPick::query()
                            ->where('order_product_id', $orderProductPick->order_product_id)
                            ->sum('quantity_picked') ?? 0,
                    'quantity_skipped_picking' => OrderProductPick::query()
                            ->where('order_product_id', $orderProductPick->order_product_id)
                            ->sum('quantity_skipped_picking') ?? 0,
                ]);

                $orderProductPick->pick->update([
                    'quantity_distributed' => $orderProductPick->pick->orderProductPicks()->sum('quantity_picked') ?? 0,
                ]);
            });
    }
}
