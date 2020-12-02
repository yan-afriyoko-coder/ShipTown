<?php

namespace App\Jobs\Maintenance\Order;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class RecalculateTotalQuantityOrderedJob implements ShouldQueue
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

            SET `'.$prefix.'orders`.`total_quantity_ordered` = IFNULL(
                (
                    SELECT sum(`'.$prefix.'order_products`.`quantity_ordered`)
                    FROM `'.$prefix.'order_products`
                    WHERE `'.$prefix.'order_products`.`order_id` = `'.$prefix.'orders`.`id`
                        AND `'.$prefix.'order_products`.`deleted_at` IS NULL
                ),
                0
            )
        ');

        info('Recalculated order total_quantity_ordered');
    }
}
