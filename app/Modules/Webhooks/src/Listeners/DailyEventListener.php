<?php

namespace App\Modules\Webhooks\src\Listeners;

use Illuminate\Support\Facades\DB;

class DailyEventListener
{
    public function handle()
    {
        DB::statement('
            UPDATE modules_webhooks_pending_webhooks
            SET reserved_at = null, published_at = null
            WHERE created_at > DATE_SUB(now(), INTERVAL 1 DAY)
            and published_at is not null
        ');
    }
}
