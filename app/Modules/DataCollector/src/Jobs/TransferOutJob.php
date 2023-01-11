<?php

namespace App\Modules\DataCollector\src\Jobs;

use App\Models\DataCollection;
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

    public function __construct($data_collection_id)
    {
        $this->data_collection_id = $data_collection_id;
    }

    public function handle()
    {
        /** @var DataCollection $dataCollection */
        $dataCollection = DataCollection::query()->find($this->data_collection_id);

        DB::transaction(function () use ($dataCollection) {
            DataCollectorService::transferOutScanned($dataCollection);
        });
    }
}
