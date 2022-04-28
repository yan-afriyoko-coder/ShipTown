<?php

namespace App\Jobs;

use App\Events\Order\ActiveOrderCheckEvent;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class FireActiveOrderCheckEventForAllActiveOrdersJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use IsMonitored;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (Cache::get('recently_ran_all_automations') === true) {
            Log::warning('FireActiveOrderCheckEventForAllActiveOrdersJob dispatched in last 60 seconds, skipping');
            return;
        }

        Cache::add('recently_ran_all_automations', true, 60);

        $orders = Order::where([
                'is_active' => true,
                'is_editing' => false
            ])->get();

        $orders->each(function (Order $order) {
            ActiveOrderCheckEvent::dispatch($order);
        });

        $this->queueData(['events_fired' => $orders->count()]);
    }
}
