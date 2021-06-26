<?php

namespace App\Modules\AutoStatusReady\src\Jobs;

use App\Models\Order;
use App\Models\OrderStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use romanzipp\QueueMonitor\Traits\IsMonitored;

/**
 * Class RefillOldOrdersToPickingJob.
 */
class SetReadyStatusWhenPackedJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use IsMonitored;

    /**
     * @var Order
     */
    private Order $order;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $order = $this->order;

        if ($order->is_packed === false) {
            return;
        }

        if ($order->status_code === 'ready') {
            return;
        }

        if ($order->isStatusCodeIn(OrderStatus::getClosedStatuses())) {
            return;
        }

        $order->log('Order fully packed, changing status to "ready"')
            ->update(['status_code' => 'ready']);
    }
}
