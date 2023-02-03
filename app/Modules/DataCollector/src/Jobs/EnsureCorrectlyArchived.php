<?php

namespace App\Modules\DataCollector\src\Jobs;

use App\Models\DataCollection;
use App\Models\DataCollectionTransferIn;
use App\Models\DataCollectionTransferOut;
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
class EnsureCorrectlyArchived implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use IsMonitored;

    public function handle()
    {
        DataCollection::onlyTrashed()->distinct()->select('data_collections.id')
            ->rightJoin('data_collection_records', function ($join) {
                $join->on('data_collection_records.data_collection_id', '=', 'data_collections.id');
                $join->on('data_collection_records.quantity_scanned', '!=', DB::raw(0));
            })
            ->where('type', DataCollectionTransferOut::class)
            ->get()
            ->each(function (DataCollection $dataCollection) {
                TransferOutJob::dispatch($dataCollection->id);
            });

        DataCollection::onlyTrashed()->distinct()->select('data_collections.id')
            ->rightJoin('data_collection_records', function ($join) {
                $join->on('data_collection_records.data_collection_id', '=', 'data_collections.id');
                $join->on('data_collection_records.quantity_scanned', '!=', DB::raw(0));
            })
            ->where('type', DataCollectionTransferIn::class)
            ->get()
            ->each(function (DataCollection $dataCollection) {
                TransferInJob::dispatch($dataCollection->id);
            });
    }
}
