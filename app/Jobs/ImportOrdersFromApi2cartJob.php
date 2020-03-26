<?php

namespace App\Jobs;

use App\Managers\CompanyConfigurationManager;
use App\Modules\Api2cart\Orders;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ImportOrdersFromApi2cartJob implements ShouldQueue
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
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        $params = [];

        $api2cart_store_key = CompanyConfigurationManager::getBridgeApiKey();
//
//        $ordersCollection = Orders::getOrdersCollection($api2cart_store_key, $params);

        $this->finishedSuccessfully = true;
    }
}
