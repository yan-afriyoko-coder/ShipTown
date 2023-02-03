<?php

namespace App\Modules\DataCollector\src\Jobs;

use App\Models\DataCollection;
use App\Models\DataCollectionRecord;
use App\Models\DataCollectionTransferOut;
use App\Modules\DataCollector\src\DataCollectorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use romanzipp\QueueMonitor\Traits\IsMonitored;

/**
 * Class SyncCheckFailedProductsJob.
 */
class TransferOutJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use IsMonitored;

    public int $data_collection_id;

    public function __construct(int $data_collection_id = null)
    {
        $this->data_collection_id = $data_collection_id;
    }

    public function handle()
    {
        /** @var DataCollection $dataCollection */
        $dataCollection = DataCollection::withTrashed()->findOrFail($this->data_collection_id);
        $dataCollection->update(['type' => DataCollectionTransferOut::class]);
        $dataCollection->delete();

        $dataCollection->records()
            ->where('quantity_scanned', '!=', DB::raw(0))
            ->chunkById(100, function ($records) {
                $records->each(function (DataCollectionRecord $record) {
                    retry(5, function () use ($record) {
                        DB::transaction(function () use ($record) {
                            DataCollectorService::transferOutRecord($record);
                        });
                    }, 1000);
                });
            });
    }
}
