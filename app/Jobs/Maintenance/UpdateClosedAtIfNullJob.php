<?php

namespace App\Jobs\Maintenance;

use App\Models\Order;
use App\Models\OrderStatus;
use App\Modules\Api2cart\src\Models\Api2cartOrderImports;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateClosedAtIfNullJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Order::whereNull('order_closed_at')
            ->whereIn('status_code', OrderStatus::getCompletedStatusCodeList())
            ->limit(500)
            ->latest()
            ->get()
            ->each(function (Order $order) {
                $orderImport = Api2cartOrderImports::where(['order_number' => $order->order_number])
                    ->latest()
                    ->first();

                foreach ($orderImport->extractStatusHistory() as $status) {
                    if (OrderStatus::isComplete($status['id'])) {
                        $order->update(['order_closed_at' => Carbon::make($status['modified_time']['value'])]);
                        break;
                    }
                }
            });
    }
}
