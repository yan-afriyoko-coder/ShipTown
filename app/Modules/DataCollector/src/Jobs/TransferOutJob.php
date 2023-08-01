<?php

namespace App\Modules\DataCollector\src\Jobs;

use App\Models\DataCollection;
use App\Models\DataCollectionRecord;
use App\Modules\DataCollector\src\Services\DataCollectorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class SyncCheckFailedProductsJob.
 */
class TransferOutJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $dataCollection_id;

    public function __construct($dataCollection_id)
    {
        $this->dataCollection_id = $dataCollection_id;
    }

    public function handle()
    {
        Log::debug('TransferOutJob started', ['data_collection_id' => $this->dataCollection_id]);

        DataCollectionRecord::query()
            ->where('data_collection_id', $this->dataCollection_id)
            ->where('quantity_scanned', '!=', DB::raw(0))
            ->chunkById(100, function ($records) {
                $records->each(function (DataCollectionRecord $record) {
                    DataCollectorService::transferOutRecord($record);
                });
            });

        Log::debug('TransferOutJob finished', ['data_collection_id' => $this->dataCollection_id]);

        if (DataCollectionRecord::query()->where('quantity_scanned', '!=', 0)->exists()) {
            return;
        }

        DataCollection::query()
            ->where('id', $this->dataCollection_id)
            ->update(['currently_running_task' => null]);

        if (DataCollectionRecord::query()->where('quantity_to_scan', '!=', 0)->exists()) {
            return;
        }

        DataCollection::query()
            ->where('id', $this->dataCollection_id)
            ->delete();
    }
}
