<?php

namespace App\Modules\OrderTotals\src\Jobs;

use App\Abstracts\UniqueJob;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class EnsureAllRecordsExistsJob extends UniqueJob
{
    private Carbon $fromDateTime;
    private Carbon $toDateTime;

    public function __construct($fromDateTime = null, $toDateTime = null)
    {
        $this->fromDateTime = $fromDateTime ?? now()->subHour();
        $this->toDateTime = $toDateTime ?? now();
    }

    public function handle()
    {
        DB::statement('
            INSERT INTO orders_products_totals (order_id, created_at)
                SELECT
                  orders.id as order_id,
                  now()

                FROM orders
                LEFT JOIN orders_products_totals ON orders_products_totals.order_id = orders.id
                WHERE orders.order_place_at BETWEEN ? AND ?
                    AND ISNULL(orders_products_totals.id)
        ', [$this->fromDateTime, $this->toDateTime]);
    }
}
