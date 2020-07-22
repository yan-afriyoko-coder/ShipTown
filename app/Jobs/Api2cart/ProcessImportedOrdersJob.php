<?php

namespace App\Jobs\Api2cart;

use App\Modules\Api2cart\src\Models\Api2cartOrderImports;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessImportedOrdersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    /**
     * Create a new job instance.
     *
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        $ordersCollection = Api2cartOrderImports::query()
            ->whereNull('when_processed')
            ->orderBy('id')
            ->get();

        foreach ($ordersCollection as $order) {
            ProcessImportedOrderJob::dispatch($order);
        }
    }
}
