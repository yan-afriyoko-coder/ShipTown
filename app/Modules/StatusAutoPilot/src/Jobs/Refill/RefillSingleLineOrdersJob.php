<?php

namespace App\Modules\StatusAutoPilot\src\Jobs\Refill;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RefillSingleLineOrdersJob implements ShouldQueue
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
        Order::where('status_code', 'paid')
            ->where('product_line_count', 1)
            ->get()->each(function ($order) {
                $order->update([
                    'status_code' => 'single_line_orders'
                ]);
            });
    }
}
