<?php

namespace App\Modules\AutoStatusAwaitingPayment\src\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SetAwaitingPaymentStatusJob implements ShouldQueue
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
        if ($this->order->isStatusCode('processing') and ($this->order->isNotPaid)) {
            $this->order->log('Order not paid in full, changing status to "awaiting_payment"');
            $this->order->update(['status_code' => 'awaiting_payment']);
        }
    }
}
