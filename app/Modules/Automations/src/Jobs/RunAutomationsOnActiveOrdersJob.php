<?php

namespace App\Modules\Automations\src\Jobs;

use App\Models\Order;
use App\Modules\Automations\src\Models\Automation;
use App\Modules\Automations\src\Services\AutomationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use romanzipp\QueueMonitor\Traits\IsMonitored;

/**
 * @property int|null order_id
 */
class RunAutomationsOnActiveOrdersJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use IsMonitored;

    private ?int $order_id;

    public function __construct(int $order_id = null)
    {
        $this->order_id = $order_id;
    }

    public function handle()
    {
        Automation::query()
            ->where('event_class', 'App\Events\Order\ActiveOrderCheckEvent')
            ->where(['enabled' => true])
            ->orderBy('priority')
            ->get()
            ->each(function (Automation $automation) {
                $orders = self::ordersSelectQuery($automation);

                $orders->each(function (Order $order) use ($automation) {
                    AutomationService::validateAndRunAutomation($automation, $order);
                });
            });
    }

    public function ordersSelectQuery(Automation $automation)
    {
        $query = Order::query()
            ->when($this->order_id, function ($query) {
                $query->where(['id' => $this->order_id]);
            })
            ->where(['is_active' => true])
            ->whereHas('orderProductsTotals', function ($query) {
                $query->whereRaw('(orders_products_totals.quantity_ordered > 0)');
            });

        return $automation->addConditions($query);
    }
}
