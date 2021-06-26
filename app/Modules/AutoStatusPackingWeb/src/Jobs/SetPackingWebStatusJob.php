<?php

namespace App\Modules\AutoStatusPackingWeb\src\Jobs;

use App\Models\Order;
use App\Services\OrderService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SetPackingWebStatusJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

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

        if ($order->status_code !== 'picking') {
            return;
        }

        // if no more orderProducts to pick exists
        if ($order->orderProducts()->where('quantity_to_pick', '>', 0)->exists()) {
            return;
        }

        $order->log('Order fully picked, changing status');
        $order->update([
            'picked_at'   => Carbon::now(),
            'status_code' => 'packing_web',
        ]);
    }
}
