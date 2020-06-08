<?php

namespace App\Jobs;

use App\Models\Api2CartConnection;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SaveOrdersCollection implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var array
     */
    private $orders = [];

    /**
     * Create a new job instance.
     *
     * @param array $orders
     */
    public function __construct(array $orders)
    {
        $this->orders = $orders;
        info('Job SaveOrdersCollection dispatched');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // save orders
        foreach ($this->orders as $order) {
            Order::query()->updateOrCreate(
                [
                    "order_number" => $order['order_number'],
                ],
                $order
            );
        }

    }
}
