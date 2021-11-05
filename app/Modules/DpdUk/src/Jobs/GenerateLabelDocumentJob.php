<?php

namespace App\Modules\DpdUk\src\Jobs;

use App\Models\OrderShipment;
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
     */
    public function handle()
    {
        ray($this->orderShipment);
    }
}
