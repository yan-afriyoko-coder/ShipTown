<?php

namespace App\Modules\DataCollector\src\Jobs;

use App\Models\DataCollection;
use App\Models\DataCollectionRecord;
use App\Models\DataCollectionTransferOut;
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

    public int $data_collection_id;

    public function __construct(int $data_collection_id = null)
    {
        $this->data_collection_id = $data_collection_id;
    }

    public function handle()
    {
        Log::debug('TransferOutJob started', ['data_collection_id' => $this->data_collection_id]);

        /** @var DataCollection $dataCollection */
        $dataCollection = DataCollection::withTrashed()->findOrFail($this->data_collection_id);
        $dataCollection->update([
            'type' => DataCollectionTransferOut::class,
            'currently_running_task' => DataCollectionTransferOut::class
        ]);

        $dataCollection->records()
            ->where('quantity_scanned', '!=', DB::raw(0))
            ->chunkById(100, function ($records) {
                $records->each(function (DataCollectionRecord $record) {
                    DataCollectorService::transferOutRecord($record);
                });
            });

        if ($dataCollection->records()->where('quantity_scanned', '!=', DB::raw(0))->exists() === false) {
            $dataCollection->update(['currently_running_task' => null]);
        }

        $everythingHasBeenTransferredOut = !$dataCollection->records()
            ->whereRaw('quantity_requested > total_transferred_out')
            ->exists();

        if ($everythingHasBeenTransferredOut) {
            $dataCollection->delete();
        }

        Log::debug('TransferOutJob finished', ['data_collection_id' => $this->data_collection_id]);
    }
}
