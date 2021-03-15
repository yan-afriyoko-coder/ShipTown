<?php

namespace App\Modules\Rmsapi\src\Jobs;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductAlias;
use App\Models\ProductPrice;
use App\Models\RmsapiConnection;
use App\Models\RmsapiProductImport;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

class ProcessImportedBatch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private ?string $batch_uuid = null;

    /**
     * Create a new job instance.
     *
     * @param String|null $batch_uuid
     */
    public function __construct(String $batch_uuid = null)
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
            ->when(isset($this->batch_uuid), function ($query) {
                return $query->where('batch_uuid', '=', $this->batch_uuid);
            })
            ->whereNull('when_processed')
            ->orderBy('id', 'asc')
            ->get();

        foreach ($imports as $importedProduct) {
            ImportProductJob::dispatch($importedProduct);
        }
    }
}
