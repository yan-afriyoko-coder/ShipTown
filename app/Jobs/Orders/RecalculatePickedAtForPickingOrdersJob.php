<?php

namespace App\Jobs\Orders;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class RecalculatePickedAtForPickingOrdersJob implements ShouldQueue
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
                    WHERE `'.$prefix.'order_products`.order_id = `'.$prefix.'orders`.id
                        AND `'.$prefix.'order_products`.`deleted_at` IS NULL
                    GROUP BY `'.$prefix.'order_products`.order_id
                    HAVING
                      SUM(`'.$prefix.'order_products`.quantity_ordered - `'.$prefix.'order_products`.quantity_picked) = 0
                )

            WHERE `'.$prefix.'orders`.status_code = "picking"
            AND `'.$prefix.'orders`.picked_at IS NULL

        ');

        DB::statement('
            UPDATE `'.$prefix.'orders`

            SET
                `'.$prefix.'orders`.status_code = "packing_web"

            WHERE `'.$prefix.'orders`.status_code = "picking"
            AND `'.$prefix.'orders`.picked_at IS NOT NULL
        ');

        info('Recalculated "picking" orders picked_at');
    }
}
