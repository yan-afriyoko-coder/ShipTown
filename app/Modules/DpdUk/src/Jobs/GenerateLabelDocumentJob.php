<?php

namespace App\Modules\DpdUk\src\Jobs;

use App\Models\OrderShipment;
use App\Modules\DpdUk\src\Models\Connection;
use App\Modules\DpdUk\src\Services\NextDayShippingService;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateLabelDocumentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var OrderShipment
     */
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
        $connection = Connection::first();

        if ($connection) {
            NextDayShippingService::printNewLabel($this->orderShipment, $connection);
        }
    }
}
