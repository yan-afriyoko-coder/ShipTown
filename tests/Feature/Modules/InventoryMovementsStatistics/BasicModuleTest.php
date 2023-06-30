<?php

namespace Tests\Feature\Modules\InventoryMovementsStatistics;

use App\Modules\InventoryMovementsStatistics\src\InventoryMovementsStatisticsServiceProvider;
use App\Modules\InventoryMovementsStatistics\src\Jobs\AccountForNewSalesJob;
use App\Modules\InventoryMovementsStatistics\src\Jobs\Remove14DaysOutdatedSalesJob;
use App\Modules\InventoryMovementsStatistics\src\Jobs\Remove28DaysOutdatedSalesJob;
use App\Modules\InventoryMovementsStatistics\src\Jobs\Remove7DaysOutdatedSalesJob;
use Tests\TestCase;

class BasicModuleTest extends TestCase
{
    /** @test */
    public function test_module_basic_functionality()
    {
        InventoryMovementsStatisticsServiceProvider::enableModule();

        AccountForNewSalesJob::dispatch();
        Remove7DaysOutdatedSalesJob::dispatch();
        Remove14DaysOutdatedSalesJob::dispatch();
        Remove28DaysOutdatedSalesJob::dispatch();

        // we just make sure jobs run without errors
        $this->assertTrue(true);
    }
}
