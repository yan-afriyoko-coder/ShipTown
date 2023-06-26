<?php

namespace Tests\Feature\Modules\InventoryMovementsStatistics;

use App\Modules\InventoryMovementsStatistics\src\InventoryMovementsStatisticsServiceProvider;
use App\Modules\InventoryMovementsStatistics\src\Jobs\ClearOutdatedStatisticsJob;
use App\Modules\InventoryMovementsStatistics\src\Jobs\EnsureCorrectLastSoldAtJob;
use App\Modules\InventoryMovementsStatistics\src\Jobs\EnsureInventoryMovementsStatisticsRecordsExistJob;
use App\Modules\InventoryMovementsStatistics\src\Jobs\UpdateInventoryMovementsStatisticsTableJob;
use Tests\TestCase;

class BasicModuleTest extends TestCase
{
    /** @test */
    public function test_module_basic_functionality()
    {
        InventoryMovementsStatisticsServiceProvider::enableModule();

        ClearOutdatedStatisticsJob::dispatch();
        EnsureCorrectLastSoldAtJob::dispatch();
        EnsureInventoryMovementsStatisticsRecordsExistJob::dispatch();
        UpdateInventoryMovementsStatisticsTableJob::dispatch();

        // we just make sure jobs run without errors
        $this->assertTrue(true);
    }
}
