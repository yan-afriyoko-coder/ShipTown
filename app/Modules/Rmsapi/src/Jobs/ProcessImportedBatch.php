<?php

namespace App\Modules\Rmsapi\src\Jobs;

use App\Modules\Rmsapi\src\Models\RmsapiProductImport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessImportedBatch implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private ?string $batch_uuid = null;

    /**
     * Create a new job instance.
     *
     * @param string|null $batch_uuid
     */
    public function __construct(string $batch_uuid = null)
    {
        $this->batch_uuid = $batch_uuid;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $imports = RmsapiProductImport::query()
            ->whereNull('when_processed')
            ->limit(500)
            ->orderBy('id', 'asc')
            ->get();

        foreach ($imports as $importedProduct) {
            ImportProductJob::dispatch($importedProduct);
        }
    }
}
