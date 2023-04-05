<?php

namespace App\Modules\Rmsapi\src\Jobs;

use App\Modules\Rmsapi\src\Models\RmsapiProductImport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class CleanupProductsImportTableJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle(): bool
    {
        RmsapiProductImport::query()
            ->whereNull('when_processed')
            ->where('reserved_at', '<', now()->subMinutes(5))
            ->update(['reserved_at' => null]);


        RmsapiProductImport::query()
            ->where('when_processed', '<', now()->subDays(7))
            ->forceDelete();

        DB::statement('
            UPDATE inventory SET inventory.last_movement_id = (SELECT MAX(ID) FROM inventory_movements WHERE inventory_id = inventory.id)
            WHERE inventory.last_movement_id != (SELECT MAX(ID) FROM inventory_movements WHERE inventory_id = inventory.id)
            LIMIT 20;
        ');

        return true;
    }
}
