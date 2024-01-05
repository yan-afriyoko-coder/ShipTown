<?php

namespace Tests\Feature\Modules\InventoryMovementsStatistics;

use App\Models\InventoryMovement;
use App\Modules\InventoryMovementsStatistics\src\Jobs\RecalculateStatisticsTableJob;
use Tests\TestCase;

class RepopulateStatisticsTableJobTest extends TestCase
{
    public function testBasic()
    {
        InventoryMovement::factory(3)->create(['type' => 'sale']);

        RecalculateStatisticsTableJob::dispatch();

        $this->assertDatabaseCount('inventory_movements_statistics', 1);
    }
}
