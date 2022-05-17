<?php

namespace App\Modules\Api2cart\src\Jobs;

use App\Models\Product;
use App\Modules\Api2cart\src\Models\Api2cartProductLink;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class RemoveProductLinksIfNotAvailableOnlineJob implements ShouldQueue
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
        Api2cartProductLink::query()
            ->whereDoesntHave('product', function ($query) {
                $query->hasTags('Available Online');
            })
            ->delete();

//        ray($query->toSql(), $query->get(), Api2cartProductLink::all());
    }
}
