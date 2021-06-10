<?php

namespace App\Jobs;

use App\Events\Order\ActiveOrderCheckEvent;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class FireActiveOrderCheckEventsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $orders = Order::whereIsActive()->get();

        $orders->each(function (Order $order) {
            ActiveOrderCheckEvent::dispatch($order);
        });

        $this->queueData(['events_fired' => $orders->count()]);
    }
}
