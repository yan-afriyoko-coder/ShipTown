<?php

namespace App\Jobs\Orders;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

class RecalculateOrderProductLineCountJob implements ShouldQueue
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
//        $prefix = config('database.connections.mysql.prefix');
//
//        DB::statement('
//            UPDATE `'.$prefix.'orders`
//
//            LEFT JOIN `'.$prefix.'products`
//                ON `'.$prefix.'products`.`sku` = `'.$prefix.'order_products`.`sku_ordered`
//                OR `'.$prefix.'products`.`sku` = LEFT(`'.$prefix.'order_products`.`sku_ordered`,6)
//
//            SET `'.$prefix.'order_products`.`product_id` =`'.$prefix.'products`.`id`
//
//            WHERE
//                `'.$prefix.'order_products`.`product_id` IS NULL
//                AND `'.$prefix.'products`.`id` IS NOT NULL
//        ');
    }
}
