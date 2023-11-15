<?php

namespace App\Modules\MagentoApi\src\Listeners;

use App\Modules\MagentoApi\src\Jobs\RecheckStockPeriodicallyJob;

class EveryDayEventListener
{
    public function handle()
    {
         RecheckStockPeriodicallyJob::dispatch();
    }
}
