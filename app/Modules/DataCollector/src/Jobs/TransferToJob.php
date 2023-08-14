<?php

namespace App\Modules\DataCollector\src\Jobs;

use App\Models\DataCollection;
use App\Modules\DataCollector\src\Services\DataCollectorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

/**
 * Class SyncCheckFailedProductsJob.
 */
class TransferToJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $uniqueFor = 60;

    public int $dataCollection_id;

    public function __construct(int $dataCollection_id)
    {
        $this->dataCollection_id = $dataCollection_id;
    }

    public function uniqueId(): int
    {
        return $this->dataCollection_id;
    }

    public function handle()
    {
        DB::transaction(function () use (&$destinationDataCollection) {
            $sourceDataCollection = DataCollection::findOrFail($this->dataCollection_id);

            DataCollectorService::transferScannedTo(
                $sourceDataCollection,
                $sourceDataCollection->destination_warehouse_id
            );
        });
    }
}
