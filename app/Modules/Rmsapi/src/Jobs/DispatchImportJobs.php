<?php

namespace App\Modules\Rmsapi\src\Jobs;

use App\Modules\Rmsapi\src\Models\RmsapiConnection;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DispatchImportJobs implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Execute the job.
     *
     * @return bool
     */
    public function handle(): bool
    {
        foreach (RmsapiConnection::all() as $rmsapiConnection) {
            ImportProductsJob::dispatch($rmsapiConnection->id);
            ImportShippingsJob::dispatch($rmsapiConnection->id);

            logger('Rmsapi sync job dispatched', [
                'warehouse_code' => $rmsapiConnection->location_id,
                'connection_id' => $rmsapiConnection->id
            ]);
        }

        return true;
    }
}
