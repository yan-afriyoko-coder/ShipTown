<?php

namespace Tests\Feature\Modules\Rmsapi;

use App\Modules\Rmsapi\src\Jobs\ProcessImportedSalesRecordsJob;
use App\Modules\Rmsapi\src\Models\RmsapiConnection;
use App\Modules\Rmsapi\src\Models\RmsapiSaleImport;
use PHPUnit\Framework\TestCase;

class ProcessImportedSalesRecordsJobTest extends TestCase
{
    public function testExample()
    {
        /** @var RmsapiConnection $rmsapiConnection */
        $rmsapiConnection = RmsapiConnection::factory()->create();

        $saleRecord = RmsapiSaleImport::query()->create([
            'connection_id' => $rmsapiConnection->id,
        ]);

        ProcessImportedSalesRecordsJob::dispatchSync($rmsapiConnection->id);


    }
}
