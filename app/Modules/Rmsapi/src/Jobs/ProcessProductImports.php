<?php

namespace App\Modules\Rmsapi\src\Jobs;

use App\Modules\Rmsapi\src\Models\RmsapiProductImport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessProductImports implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        RmsapiProductImport::query()
            ->whereNull('when_processed')
            ->where('reserved_at', '<', now()->subMinutes(10))
            ->update(['reserved_at' => null]);

        RmsapiProductImport::query()
            ->whereNull('when_processed')
            ->whereNull('reserved_at')
            ->limit(500)
            ->orderBy('id', 'asc')
            ->get()->each(function (RmsapiProductImport $productImport) {
                $productImport->update(['reserved_at' => now()]);
                ImportProductJob::dispatch($productImport);
            });
    }
}
