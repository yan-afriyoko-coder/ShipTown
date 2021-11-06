<?php

namespace App\Modules\DpdUk\src\Jobs;

use App\Models\OrderShipment;
use App\Modules\DpdUk\src\Models\Connection;
use App\Modules\DpdUk\src\Services\DpdUkService;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateLabelDocumentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private OrderShipment $orderShipment;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(OrderShipment $orderShipment)
    {
        $this->orderShipment = $orderShipment;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws GuzzleException
     */
    public function handle()
    {
        if (env('TEST_DPDUK_USERNAME')) {
            /** @var Connection $connection */
            $connection = Connection::query()->firstOrCreate([], [
                'username' => env('TEST_DPDUK_USERNAME'),
                'password' => env('TEST_DPDUK_PASSWORD'),
                'account_number' => env('TEST_DPDUK_ACCNUMBER'),
            ]);
        } else {
            $connection = Connection::first();
        }

        if ($connection) {
            DpdUkService::printNewLabel($this->orderShipment, $connection);
        }
    }
}
