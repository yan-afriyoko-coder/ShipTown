<?php

namespace App\Modules\DataCollectorSalePrices\src\Jobs;

use App\Abstracts\UniqueJob;
use App\Models\DataCollectionRecord;
use Illuminate\Support\Facades\Cache;

class AddSalePriceIfApplicable extends UniqueJob
{
    private DataCollectionRecord $dataCollectionRecord;

    public function uniqueId(): string
    {
        return implode('_', [self::class, $this->dataCollectionRecord->id]);
    }

    public function __construct(DataCollectionRecord $dataCollectionRecord)
    {
        $this->dataCollectionRecord = $dataCollectionRecord;
    }

    public function handle(): void
    {
        $cacheLockKey = implode('-', ['adding_sale_price_to_data_collection_record', $this->dataCollectionRecord->id]);

        Cache::lock($cacheLockKey, 5)->get(function () {
            $this->addSalePrice();
        });
    }

    public function addSalePrice(): void
    {
        $prices = data_get($this->dataCollectionRecord, 'prices');
        if (empty($prices) || !empty($this->dataCollectionRecord->price_source)) {
            return;
        }

        if ($prices['current_price'] <= $this->dataCollectionRecord->unit_sold_price) {
            $this->dataCollectionRecord->update([
                'unit_sold_price' => $prices['current_price'],
            ]);
        }

        $this->dataCollectionRecord->updateQuietly([
            'price_source' => $prices['price'] === $prices['current_price'] ? 'FULL_PRICE' : 'SALE_PRICE',
        ]);
    }
}
