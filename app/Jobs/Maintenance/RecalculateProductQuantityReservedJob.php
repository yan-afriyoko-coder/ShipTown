<?php

namespace App\Jobs\Maintenance;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class RecalculateProductQuantityReservedJob implements ShouldQueue
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
            UPDATE `'.$prefix.'products`

            SET `'.$prefix.'products`.`quantity_reserved` = IFNULL(
                (
                    SELECT sum(`'.$prefix.'inventory`.`quantity_reserved`)
                    FROM `'.$prefix.'inventory`
                    WHERE `'.$prefix.'inventory`.`product_id` = `'.$prefix.'products`.`id`
                ),
                0
            )
        ');
    }
}
