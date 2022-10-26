<?php

namespace App\Modules\Rmsapi\src\Listeners;

use App\Modules\Rmsapi\src\Models\RmsapiProductImport;

class HourlyEventListener
{
    public function handle()
    {
        RmsapiProductImport::query()
            ->where('when_processed', '<', now()->subDays(7))
            ->forceDelete();
    }
}
