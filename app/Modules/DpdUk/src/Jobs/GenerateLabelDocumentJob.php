<?php

namespace App\Modules\DpdUk\src\Jobs;

use App\Events\OrderShipment\OrderShipmentCreatedEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateLabelDocumentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private OrderShipmentCreatedEvent $event;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(OrderShipmentCreatedEvent $event)
    {
        $this->event = $event;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
    }
}
