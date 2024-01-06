<?php

namespace App\Modules\DataCollector\src\Jobs;

use App\Abstracts\UniqueJob;
use App\Models\DataCollection;
use App\Modules\DataCollector\src\Services\DataCollectorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class SyncCheckFailedProductsJob.
 */
class TransferToJob extends UniqueJob
{
    public int $dataCollection_id;

    public function __construct(int $dataCollection_id)
    {
        $this->dataCollection_id = $dataCollection_id;
    }

    public function uniqueId(): string
    {
        return implode('-', [get_class($this), $this->dataCollection_id]);
    }

    public function handle(): void
    {
        /** @var DataCollection $sourceDataCollection */
        $sourceDataCollection = DataCollection::withTrashed()->findOrFail($this->dataCollection_id);

        DataCollectorService::transferScannedTo(
            $sourceDataCollection,
            $sourceDataCollection->destination_warehouse_id
        );
    }
}
