<?php

namespace App\Jobs\Maintenance;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class RecalculateOrderProductQuantityPicked implements ShouldQueue
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
            UPDATE `'.$prefix.'order_products`

            SET `'.$prefix.'order_products`.`quantity_picked` = IFNULL(
                (
                    SELECT sum(`'.$prefix.'pick_requests`.`quantity_picked`)
                    FROM `'.$prefix.'pick_requests`
                    WHERE
                        `'.$prefix.'pick_requests`.`order_product_id` = `'.$prefix.'order_products`.`id`
                ),
                0
            )
        ');
    }
}
