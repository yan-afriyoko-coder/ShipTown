<?php

namespace App\Modules\DataCollectorQuantityDiscounts\src\Jobs;

use App\Abstracts\UniqueJob;
use App\Models\DataCollection;
use App\Modules\DataCollector\src\Services\DataCollectorService;
use App\Modules\DataCollectorQuantityDiscounts\src\Models\QuantityDiscount;
use App\Modules\DataCollectorQuantityDiscounts\src\Services\QuantityDiscountsService;
use Illuminate\Support\Facades\Cache;

class CalculateSoldPriceForMultibuyPercentDiscount extends UniqueJob
{
    private QuantityDiscount $discount;
    private DataCollection $dataCollection;

    public function uniqueId(): string
    {
        return implode('_', [self::class, $this->dataCollection->id]);
    }

    public function __construct(DataCollection $dataCollection, QuantityDiscount $discount)
    {
        $this->discount = $discount;
        $this->dataCollection = $dataCollection;
    }

    public function handle(): void
    {
        $cacheLockKey = implode('-', [
            'recalculating_quantity_discounts_for_data_collection',
            $this->dataCollection->id
        ]);

        Cache::lock($cacheLockKey, 5)->get(function () {
            QuantityDiscountsService::preselectEligibleRecords($this->dataCollection, $this->discount);
            $this->applyDiscountsToSelectedRecords();
            DataCollectorService::recalculate($this->dataCollection);
        });
    }

    public function applyDiscountsToSelectedRecords(): void
    {
        $eligibleRecords = QuantityDiscountsService::getRecordsEligibleForDiscount($this->dataCollection, $this->discount)
            ->where(['price_source_id' => $this->discount->id])
            ->get();

        if ($this->discount->is_multibuy_discount) {
            $config = collect($this->discount->configuration['multibuy_discount_ranges']);
            $quantityToDistribute = $eligibleRecords->sum('quantity_scanned') < $config->min('minimum_quantity')
                ? 0
                : $eligibleRecords->sum('quantity_scanned');
            $correctDiscount = $config
                ->where('minimum_quantity', '<=', $quantityToDistribute)
                ->sortByDesc('minimum_quantity')
                ->first();

            $discountedPercent = $correctDiscount['discount_percent'] ?? 0;
        } else {
            $quantityToDistribute = $this->discount->quantity_required * QuantityDiscountsService::timesWeCanApplyOfferFor($eligibleRecords, $this->discount);
            $discountedPercent = $this->discount->configuration['discount_percent'];
        }

        QuantityDiscountsService::applyDiscounts(
            $eligibleRecords,
            $quantityToDistribute,
            function ($record) use ($discountedPercent) {
                return $record->unit_full_price - ($record->unit_full_price * ($discountedPercent / 100));
            }
        );
    }
}
