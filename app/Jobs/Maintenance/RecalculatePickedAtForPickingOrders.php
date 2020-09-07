<?php

namespace App\Jobs\Maintenance;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class RecalculatePickedAtForPickingOrders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $prefix = config('database.connections.mysql.prefix');

        DB::statement('
            UPDATE `'.$prefix.'orders`

            SET `'.$prefix.'orders`.`picked_at` =
                (
                    SELECT max(`'.$prefix.'order_products`.updated_at)
                    FROM `'.$prefix.'order_products`
                    GROUP BY `'.$prefix.'order_products`.order_id
                    HAVING
                      SUM(`'.$prefix.'order_products`.quantity_ordered - `'.$prefix.'order_products`.quantity_picked) = 0
                )

            WHERE `'.$prefix.'orders`.status_code = "picking"
            AND `'.$prefix.'orders`.picked_at IS NULL

        ');
    }
}
