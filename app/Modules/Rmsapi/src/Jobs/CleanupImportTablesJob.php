<?php

namespace App\Modules\Rmsapi\src\Jobs;

use App\Modules\Rmsapi\src\Models\RmsapiProductImport;
use App\Modules\Rmsapi\src\Models\RmsapiSaleImport;
use App\Modules\Rmsapi\src\Models\RmsapiShippingImports;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CleanupImportTablesJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle(): bool
    {
        // Cleanup products import table
        RmsapiProductImport::query()
            ->whereNull('when_processed')
            ->where('reserved_at', '<', now()->subMinutes(5))
            ->update(['reserved_at' => null]);

        RmsapiProductImport::query()
            ->where('when_processed', '<', now()->subDays(7))
            ->forceDelete();

        // Cleanup sales imports
        RmsapiSaleImport::query()
            ->whereNull('when_processed')
            ->where('reserved_at', '<', now()->subMinutes(5))
            ->update(['reserved_at' => null]);

        RmsapiSaleImport::query()
            ->where('when_processed', '<', now()->subDays(7))
            ->forceDelete();

        // Cleanup shipping imports
        RmsapiShippingImports::query()
            ->whereNull('when_processed')
            ->where('reserved_at', '<', now()->subMinutes(5))
            ->update(['reserved_at' => null]);

        RmsapiShippingImports::query()
            ->where('when_processed', '<', now()->subDays(7))
            ->forceDelete();

        return true;
    }
}
