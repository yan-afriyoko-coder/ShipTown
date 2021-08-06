<?php


namespace App\Modules\AutoStatusLayaway\src\Jobs;

use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SetLayawayStatusJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private Order $order;

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
        if (($this->order->status_code === 'paid') and (OrderService::canNotFulfill($this->order, 4))) {
            $this->order->log('Cannot fulfill from location 4, changing to layaway');
            $this->order->update(['status_code' => 'layaway']);
        }
    }
}
