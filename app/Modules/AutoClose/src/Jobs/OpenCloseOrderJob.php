<?php

namespace App\Modules\AutoClose\src\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class OpenCloseOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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

        if ($order->isAttributeNotChanged('status_code')) {
            return;
        }

        $closesOrder = !$order->order_status->order_active;

        if ($closesOrder and ($order->order_closed_at === null)) {
            $order->log('status "'. $order->status_code.'" closing order ')
                ->update([
                    'is_active'       => false,
                    'order_closed_at' => now(),
                ]);
        }
    }
}
