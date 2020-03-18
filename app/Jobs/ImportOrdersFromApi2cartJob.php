<?php

namespace App\Jobs;

use App\Managers\UserConfigurationManager;
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
     * @var int
     */
    private $user_id;

    /**
     * Create a new job instance.
     *
     * @param int $user_id
     */
    public function __construct(int $user_id)
    {
        $this->user_id = $user_id;
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

        $api2cart_store_key = UserConfigurationManager::getValue("api2cart_store_key", $this->user_id);

        $ordersCollection = Orders::getOrdersCollection($api2cart_store_key, $params);
    }
}
