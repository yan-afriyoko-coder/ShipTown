<?php

namespace App\Modules\Rmsapi\src\Jobs;

use App\Traits\IsMonitored;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RunWarehouseJobs implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use IsMonitored;

    private int $connection_id;

    public function __construct(int $connection_id)
    {
        $this->connection_id = $connection_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        ImportSalesJob::dispatchSync($this->connection_id);
        ProcessImportedSalesRecordsJob::dispatchSync($this->connection_id);

        ImportProductsJob::dispatchSync($this->connection_id);
        ProcessImportedProductRecordsJob::dispatch($this->connection_id);

        ImportShippingsJob::dispatch($this->connection_id);
    }
}
