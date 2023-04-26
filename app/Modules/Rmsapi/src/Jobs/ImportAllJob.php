<?php

namespace App\Modules\Rmsapi\src\Jobs;

use App\Models\Heartbeat;
use App\Modules\Rmsapi\src\Api\Client as RmsapiClient;
use App\Modules\Rmsapi\src\Models\RmsapiConnection;
use App\Modules\Rmsapi\src\Models\RmsapiProductImport;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

class ImportAllJob implements ShouldQueue, ShouldBeUniqueUntilProcessing
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public string $batch_uuid;

    public int $uniqueFor = 120;

    public function uniqueId(): string
    {
        return implode('-', [get_class($this)]);
    }

    public function __construct()
    {
        $this->batch_uuid = Uuid::uuid4()->toString();
    }

    /**
     * Execute the job.
     *
     * @return boolean
     *
     * @throws Exception
     */
    public function handle(): bool
    {
        foreach (RmsapiConnection::all() as $rmsapiConnection) {
            ImportSalesJob::dispatchSync($rmsapiConnection->id);
            ImportShippingsJob::dispatchSync($rmsapiConnection->id);
            ImportAllJob::dispatchSync($rmsapiConnection->id);

            Log::debug('RMSAPI Sync jobs dispatched', [
                'warehouse_code' => $rmsapiConnection->location_id,
                'connection_id' => $rmsapiConnection->id
            ]);
        }
    }
}
