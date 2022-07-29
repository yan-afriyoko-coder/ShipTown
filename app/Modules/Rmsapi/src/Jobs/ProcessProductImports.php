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
            ->where('reserved_at', '<', now()->subMinutes(60))
            ->update(['reserved_at' => null]);

        $query = RmsapiProductImport::query()
            ->whereNull('when_processed')
            ->whereNull('reserved_at')
            ->limit(20)
            ->orderBy('id', 'asc');

        $productImports = $query->get();

        while ($productImports->isNotEmpty()) {
            RmsapiProductImport::query()
                ->whereIn('id', $productImports->pluck('id'))
                ->update(['reserved_at' => now()]);

            $productImports->each(function (RmsapiProductImport $productImport) {
                ProcessProductImportJob::dispatchNow($productImport);
            });

            $productImports = $query->get();
        }
    }
}
