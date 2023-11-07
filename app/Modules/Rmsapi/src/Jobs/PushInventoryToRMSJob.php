<?php

namespace App\Modules\Rmsapi\src\Jobs;

use App\Modules\Rmsapi\src\Api\Client;
use App\Modules\Rmsapi\src\Models\RmsapiConnection;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

class PushInventoryToRMSJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public string $batch_uuid;

    public int $uniqueFor = 300;

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
     * @throws Exception|GuzzleException
     */
    public function handle(): bool
    {
        /** @var RmsapiConnection $rmsConnection */
        $rmsConnection = RmsapiConnection::first();

        Client::POST($rmsConnection, 'command', [
            'command' => $this->batch_uuid,
        ]);

        return true;
    }
}
