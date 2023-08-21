<?php

namespace App\Modules\DataCollector\src\Jobs;

use App\Models\DataCollection;
use App\Models\DataCollectionRecord;
use App\Modules\DataCollector\src\Services\DataCollectorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class SyncCheckFailedProductsJob.
 */
class TransferOutJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $uniqueFor = 60;

    public int $dataCollection_id;

    public function __construct($dataCollection_id)
    {
        $this->dataCollection_id = $dataCollection_id;
    }

    public function uniqueId(): int
    {
        return $this->dataCollection_id;
    }

    public function handle()
    {
        Log::debug('TransferOutJob started', ['data_collection_id' => $this->dataCollection_id]);

        /** @var DataCollection $dataCollection */
        $dataCollection = DataCollection::withTrashed()->findOrFail($this->dataCollection_id);

        $dataCollection->records()
            ->where('quantity_scanned', '!=', DB::raw(0))
            ->chunkById(10, function ($records) {
                $records->each(function (DataCollectionRecord $record) {
                    DataCollectorService::transferOutRecord($record);
                });
            });

        $hasMoreRecordsToTransfer = $dataCollection->records()
            ->where('quantity_scanned', '!=', DB::raw(0))
            ->exists();

        if ($hasMoreRecordsToTransfer) {
            return;
        } else {
            $dataCollection->update(['currently_running_task' => null]);
            Log::debug('TransferOutJob finished', ['data_collection_id' => $this->dataCollection_id]);
        }

        if ($dataCollection->records()->where('quantity_to_scan', '>', 0)->doesntExist()) {
            $dataCollection->delete();
        }
    }
}
