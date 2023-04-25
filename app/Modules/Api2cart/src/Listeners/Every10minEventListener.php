<?php

namespace App\Modules\Api2cart\src\Listeners;

use App\Events\Every10minEvent;
use App\Modules\Api2cart\src\Jobs\CheckForOutOfSyncProductsJob;
use App\Modules\Api2cart\src\Jobs\DispatchImportOrdersJobs;
use App\Modules\Api2cart\src\Jobs\FetchSimpleProductsInfoJob;
use App\Modules\Api2cart\src\Jobs\FetchVariantsInfoJob;
use App\Modules\Api2cart\src\Jobs\ProcessImportedOrdersJob;
use App\Modules\Api2cart\src\Jobs\UpdateMissingTypeAndIdJob;
use Illuminate\Support\Facades\DB;

class Every10minEventListener
{

}
