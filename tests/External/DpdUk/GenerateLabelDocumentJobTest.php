<?php

namespace Tests\External\DpdUk;

use App\Events\OrderShipment\OrderShipmentCreatedEvent;
use App\Models\OrderAddress;
use App\Models\OrderShipment;
use App\Modules\DpdUk\src\DpdUkServiceProvider;
use App\Modules\DpdUk\src\Jobs\GenerateLabelDocumentJob;
use App\Modules\DpdUk\src\Models\Connection;
use App\Modules\DpdUk\src\Services\DpdUkService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class GenerateLabelDocumentJobTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var string
     */
    private string $testSkippingExpiryDate = '01 May 2022';

    public function test_print_new_label()
    {
        if (Carbon::make($this->testSkippingExpiryDate)->isFuture()) {
            $this->markTestSkipped();
        }

        /** @var OrderAddress $testAddress */
        $testAddress = factory(OrderAddress::class)->make();
        $testAddress->first_name      = 'My';
        $testAddress->last_name       = 'Contact';
        $testAddress->phone           = '0121 500 2500';
        $testAddress->company         = "DPD Group Ltd";
        $testAddress->country_code    = "GB";
        $testAddress->postcode        = "B66 1BY";
        $testAddress->address1        = "Roebuck Lane";
        $testAddress->address2        = "Smethwick";
        $testAddress->city            = "Birmingham";
        $testAddress->state_code      = "West Midlands";
        $testAddress->save();

        /** @var Connection $connection */
        $connection = factory(Connection::class)->make();
        $connection->collection_address_id = $testAddress->getKey();
        $connection->save();

        /** @var OrderShipment $shipment */
        $shipment = factory(OrderShipment::class)->create();
        $shipment->order->shippingAddress()->associate($testAddress)->save();

        $this->assertNotNull(DpdUkService::printNewLabel($shipment, $connection));
    }

    public function test_if_job_dispatches()
    {
        if ($this->testSkippingExpiryDate) {
            $this->markTestSkipped();
        }

        DpdUkServiceProvider::enableModule();

        Bus::fake();

        OrderShipmentCreatedEvent::dispatch(factory(OrderShipment::class)->create());

        Bus::assertDispatched(GenerateLabelDocumentJob::class);
    }
}
