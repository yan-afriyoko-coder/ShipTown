<?php

namespace App\Jobs\Maintenance;

use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use PHP_CodeSniffer\Reports\Info;

class MakeSureOrdersAreOnPicklist implements ShouldQueue
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
        $orders = Order::query()
            ->where(['status_code' => 'picking'])
            ->get();

        foreach ($orders as $order) {
            OrderService::createPickRequests($order);
        }

        \info('Forced "picking" orders to picks requests');
    }
}
