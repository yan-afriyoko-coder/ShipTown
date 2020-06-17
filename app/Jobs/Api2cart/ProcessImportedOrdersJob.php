<?php

namespace App\Jobs\Api2cart;

use App\Models\Api2cartOrderImports;
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
     * @var bool
     */
    public $finishedSuccessfully;

    /**
     * Create a new job instance.
     *
     */
    public function __construct()
    {
        $this->finishedSuccessfully = false;
        logger('Job Api2cart\ProcessImportedOrders dispatched');
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
            \App\Jobs\ChainedJobWrapper::withChain([
                (new ImportProductsJob($order)), // Make sure products are imported first
                (new ProcessImportedOrderJob($order))
            ])->dispatch();
        }

        // finalize
        $this->finishedSuccessfully = true;
    }
}
