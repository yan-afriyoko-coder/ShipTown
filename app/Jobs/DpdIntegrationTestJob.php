<?php

namespace App\Jobs;

use App\Models\Order;
use App\Modules\DpdIreland\Dpd;
use App\Modules\DpdIreland\src\Exceptions\ConsignmentValidationException;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use function Clue\StreamFilter\fun;

class DpdIntegrationTestJob implements ShouldQueue
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
            ->inRandomOrder()
            ->limit(10)
            ->get()
            ->map(function ($order) {
                try {
                    $preAdvice = Dpd::shipOrder($order);
                    info('DPD label OK', [
                        'order' => $order->order_number,
                        'preAdvice' => $preAdvice->toArray()['Consignment'],
                    ]);
                } catch (\Exception $exception) {
                    Log::warning('DPD label FAILED', [
                        'order' => $order->order_number,
                        'exception' => [
                            'code' => $exception->getCode(),
                            'message' => $exception->getMessage(),
                        ],
                        'address' => $order->shippingAddress()->first()->toArray(),
                    ]);
                }
            });
    }
}
