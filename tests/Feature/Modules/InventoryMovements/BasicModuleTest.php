<?php

namespace Tests\Feature\Modules\InventoryMovements;

use App\Modules\InventoryMovements\src\Jobs\InventoryLastMovementIdJob;
use App\Modules\InventoryMovements\src\Jobs\InventoryQuantityJob;
use App\Modules\InventoryMovements\src\Jobs\PreviousMovementIdJob;
use App\Modules\InventoryMovements\src\Jobs\QuantityAfterJob;
use App\Modules\InventoryMovements\src\Jobs\QuantityBeforeJob;
use App\Modules\InventoryMovements\src\Jobs\QuantityDeltaJob;
use Tests\TestCase;

class BasicModuleTest extends TestCase
{
    /** @test */
    public function testBasicFunctionality()
    {
        PreviousMovementIdJob::dispatch();
        QuantityBeforeJob::dispatch();
        QuantityDeltaJob::dispatch();
        QuantityAfterJob::dispatch();
        InventoryLastMovementIdJob::dispatch();
        InventoryQuantityJob::dispatch();

        $this->assertTrue(true, 'We did not run into any errors');
    }
}
