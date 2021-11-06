<?php

namespace Tests\Feature\Modules\DpdUk;

use App\Events\OrderShipment\OrderShipmentCreatedEvent;
use App\Models\OrderAddress;
use App\Models\OrderShipment;
use App\Modules\DpdUk\src\DpdUkServiceProvider;
use App\Modules\DpdUk\src\Jobs\GenerateLabelDocumentJob;
use App\Modules\DpdUk\src\Models\Connection;
use App\Modules\DpdUk\src\Services\DpdUkService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class GenerateLabelDocumentJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_if_job_dispatches()
    {
        DpdUkServiceProvider::enableModule();

        Bus::fake();

        OrderShipmentCreatedEvent::dispatch(factory(OrderShipment::class)->create());

        Bus::assertDispatched(GenerateLabelDocumentJob::class);
    }
}
