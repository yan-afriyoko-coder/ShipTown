<?php

namespace App\Jobs;

use App\Models\ConfigurationApi2cart;
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
    private $ordersCollection = [];

    /**
     * Create a new job instance.
     *
     * @param array $ordersCollection
     */
    public function __construct(array $ordersCollection)
    {
        $this->ordersCollection = $ordersCollection;
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
        foreach ($this->ordersCollection as $order) {
            Order::query()->updateOrCreate(
                [
                    "order_number" => $order['order_number'],
                ],
                array_merge(
                    $order,
                    [
                        'raw_import' => $order
                    ]
                )
            );
        }

    }
}
