<?php

namespace Tests\Feature\Modules\Rmsapi;

use App\Modules\Rmsapi\src\Jobs\ProcessImportedSalesRecordsJob;
use App\Modules\Rmsapi\src\Models\RmsapiConnection;
use App\Modules\Rmsapi\src\Models\RmsapiSaleImport;
use Tests\TestCase;

class ProcessImportedSalesRecordsJobTest extends TestCase
{
    public function testExample()
    {
        /** @var RmsapiConnection $rmsapiConnection */
        $rmsapiConnection = RmsapiConnection::factory()->create();

        /** @var RmsapiSaleImport $saleRecord */
        $saleRecord = RmsapiSaleImport::create([
            'connection_id' => $rmsapiConnection->id,
            'comment' => 'PM_OrderProductShipment_1234567890'
        ]);

        ProcessImportedSalesRecordsJob::dispatchSync($rmsapiConnection->id);

        $this->assertDatabaseMissing('modules_rmsapi_sales_imports', [
            'id' => $saleRecord->id,
            'processed_at' => null,
        ]);
    }
}
