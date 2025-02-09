<?php

namespace App\Modules\DataCollectorGroupRecords\src\Jobs;

use App\Abstracts\UniqueJob;
use App\Models\DataCollection;
use App\Models\DataCollectionRecord;
use App\Models\DataCollectionTransaction;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class GroupRecordsJob extends UniqueJob
{
    private DataCollection $dataCollection;

    public function uniqueId(): string
    {
        return implode('_', [self::class, $this->dataCollection->id]);
    }

    public function __construct(DataCollection $dataCollection)
    {
        $this->dataCollection = $dataCollection;
    }

    public function handle(): void
    {
        $cacheLockKey = implode('-', ['grouping_similar_records_in_data_collection', $this->dataCollection->id]);

        Cache::lock($cacheLockKey, 5)->get(function () {
            $this->groupSimilarProducts();
        });
    }

    private function getGroupByAttributes($item): array
    {
        return match ($this->dataCollection->type) {
            DataCollectionTransaction::class => [
                $item['inventory_id'],
                $item['unit_cost'],
                $item['unit_sold_price'],
                $item['price_source'],
                $item['price_source_id'],
                $item['custom_uuid'],
            ],
            default => [
                $item['inventory_id'],
                $item['price_source'],
                $item['price_source_id'],
                $item['custom_uuid'],
            ],
        };
    }

    public function groupSimilarProducts(): void
    {
        $groupedRecords = $this->dataCollection->records()
            ->getQuery()
            ->whereNull('deleted_at')
            ->get()
            ->groupBy(function ($item) {
                return implode('|', $this->getGroupByAttributes($item));
            });

        $groupedRecords->each(function (Collection $items) {
            if ($items->count() === 1) {
                return true;
            }

            /** @var DataCollectionRecord $firstProduct */
            $firstProduct = $items->pop();

            $items->each(function ($item) use ($firstProduct) {
                $firstProduct->update([
                    'quantity_scanned' => $firstProduct->quantity_scanned + $item->quantity_scanned,
                    'total_transferred_in' => $firstProduct->total_transferred_in + $item->total_transferred_in,
                    'total_transferred_out' => $firstProduct->total_transferred_out + $item->total_transferred_out,
                    'quantity_requested' => $firstProduct->quantity_requested + $item->quantity_requested,
                ]);

                $item->delete();
            });

            return true;
        });
    }
}
