<?php

namespace App\Modules\AutoStatus\src\Jobs\Refill;

use App\Models\Order;
use App\Services\AutoPilot;
use App\Services\OrderService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RefillPickingMissingStockJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var int
     */
    private $maxDailyAllowed;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->maxDailyAllowed = AutoPilot::getBatchSize();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $currentOrdersInProcessCount = Order::whereIn('status_code', ['picking'])->count();

        logger('Refilling "picking" status (warehouse stock)', [
            'currently_in_process' => $currentOrdersInProcessCount,
            'max_daily_allowed' => $this->maxDailyAllowed,
        ]);

        if ($currentOrdersInProcessCount >= $this->maxDailyAllowed) {
            return;
        }

        $orders = Order::whereStatusCode('paid')
            ->orderBy('created_at')
            ->get();

        foreach ($orders as $order) {
            if (OrderService::canNotFulfill($order, 100)) {
                $order->update(['status_code' => 'picking']);
                $currentOrdersInProcessCount++;
                info('RefillPickingMissingStockJob: updated status to picking', ['order_number' => $order->order_number]);
            }
            if ($currentOrdersInProcessCount >= $this->maxDailyAllowed) {
                break;
            }
        }
    }
}
