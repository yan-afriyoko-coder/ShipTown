<?php

namespace App\Modules\DataCollectorSalePrices\src\Jobs;

use App\Abstracts\UniqueJob;
use App\Events\DataCollection\DataCollectionRecalculateRequestEvent;
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
            DataCollectionRecalculateRequestEvent::dispatch($this->dataCollectionRecord->dataCollection);
        });
    }

    public function addSalePrice(): void
    {
        $prices = data_get($this->dataCollectionRecord, 'prices');
        $records = $this->dataCollectionRecord->dataCollection->records()
            ->getQuery()
            ->whereNull('price_source_id')
            ->whereNull('price_source')
            ->get();
        ray($records)->label('records');

        $records->each(function (DataCollectionRecord $record) use ($prices) {
            if ($prices['current_price'] <= $record->unit_sold_price && $record->price_source !== 'SALE_PRICE') {
                $record->update([
                    'unit_sold_price' => $prices['current_price'],
                    'price_source' => 'SALE_PRICE',
                ]);
            }
        });
    }

//    public function addSalePrice(): void
//    {
//        $prices = data_get($this->dataCollectionRecord, 'prices');
////        ray($this->dataCollectionRecord->price_source);
////        ray()->clearAll();
//        ray("price source id: {$this->dataCollectionRecord->price_source_id}");
////        ray($this->dataCollectionRecord->price_source_id);
//        if (empty($prices) || $this->dataCollectionRecord->price_source_id || in_array($this->dataCollectionRecord->price_source, ['FULL_PRICE', 'SALE_PRICE'])) {
//            return;
//        }
//
//        ray('here');
//        ray($prices)->label('prices');
////        if ($prices['current_price'] < $this->dataCollectionRecord->unit_sold_price) {
////            ray('here????');
////        } else {
////            $this->dataCollectionRecord->update([
////                'price_source' => 'FULL_PRICE',
////            ]);
////        }
////        if ($prices['current_price'] < $this->dataCollectionRecord->unit_sold_price) {
////            $this->dataCollectionRecord->update([
////                'unit_sold_price' => $prices['current_price'],
////            ]);
////        }
//        $this->dataCollectionRecord->update([
//            'unit_sold_price' => $prices['current_price'] < $this->dataCollectionRecord->unit_sold_price
//                ? $prices['current_price']
//                : $this->dataCollectionRecord->unit_sold_price,
//            'price_source' => $prices['price'] === $prices['current_price']
//                ? 'FULL_PRICE'
//                : 'SALE_PRICE'
//        ]);
//        ray($this->dataCollectionRecord);
//    }
}
