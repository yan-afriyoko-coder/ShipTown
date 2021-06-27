<?php

namespace App\Modules\AutoStatusPackingWarehouse\src\Jobs;

use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SetStatusPackingWarehouseJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private Order $order;

    private int $location_id;
    private string $new_status;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->location_id = 99;
        $this->new_status = 'packing_warehouse';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->hasRightStatus() and $this->canFulfill()) {
            $this->order->log("Possible to fulfill, changing status (location ". $this->location_id.')');
            $this->order->update(['status_code' => $this->new_status]);
        }
    }

    /**
     * @return bool
     */
    private function hasRightStatus(): bool
    {
        return in_array($this->order->status_code, ['paid', 'single_line_orders', 'packing_web']);
    }

    /**
     * @return bool
     */
    private function canFulfill(): bool
    {
        return OrderService::canFulfill($this->order, $this->location_id);
    }
}
