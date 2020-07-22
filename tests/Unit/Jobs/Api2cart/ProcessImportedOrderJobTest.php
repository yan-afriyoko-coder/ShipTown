<?php

namespace Tests\Unit\Jobs\Api2cart;

use App\Events\OrderCreatedEvent;
use App\Jobs\Api2cart\ProcessImportedOrderJob;
use App\Modules\Api2cart\src\Models\Api2cartOrderImports;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProcessImportedOrderJobTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        Event::fake();

        $importedOrder = factory(Api2cartOrderImports::class)->create();

        ProcessImportedOrderJob::dispatch($importedOrder);

        Event::assertDispatched(OrderCreatedEvent::class);
    }
}
