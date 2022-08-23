<?php

namespace Tests\Feature\Modules\Rmsapi;

use App\Models\Product;
use App\Models\Warehouse;
use App\Modules\Rmsapi\src\Jobs\ImportShippingsJob;
use App\Modules\Rmsapi\src\Models\RmsapiConnection;
use Tests\TestCase;

class ImportShippingsTest extends TestCase
{
    /** @test */
    public function test_module_basic_functionality()
    {
        /** @var Product $product */
        $product = factory(Product::class)->create();

        /** @var Warehouse $warehouse */
        $warehouse = factory(Warehouse::class)->create();

        $connection = factory(RmsapiConnection::class)->create(['location_id' => $warehouse->code]);

        $job = new ImportShippingsJob($connection->getKey());
        $job->importShippingRecords([
            [
                'DBTimeStamp' => 1,
                'ID' => 1,
                'ShippingDateCreated' => '2020-01-01 00:00:00',
                'TransactionNumber' => '1',
                'TransactionComment' => 'test comment',
                'ItemLookupCode' => $product->sku,
                'ItemDescription' => 'test description',
                'TransactionEntryQuantity' => 1,
                'TransactionEntryPrice' => 1,
                'TransactionEntryComment' => 'test transaction entry comment',
                'ShippingCharge' => 1,
                'ShippingCarrierName' => 'PM',
                'ShippingServiceName' => 'PM',
                'ShippingNotes' => 'test shipping note',
                'Name' => 'test name',
                'Company' => 'test company',
                'Address' => 'test address',
                'Address2' => 'test address2',
                'City' => 'test city',
                'State' => 'test state',
                'Zip' => 'test zip',
                'Country' => 'test country',
                'PhoneNumber' => 'test phone number',
                'EmailAddress' => 'test@email.com',
                'CarrierName' => 'PM',
                'ServiceName' => 'PM',
            ],
        ]);

        $this->assertDatabaseHas('inventory_movements', [
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'quantity_delta' => 1,
            'description' => 'rmsapi_shipping_import',
        ]);
    }
}
