<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateApi2cartConnctionProductIdUniqueIndex extends Migration
{
    public function up()
    {
        DB::statement('
            DELETE FROM modules_api2cart_product_links WHERE api2cart_product_id IN (
            SELECT * FROM (SELECT product_link.api2cart_product_id
                FROM modules_api2cart_product_links as product_link
                GROUP BY api2cart_product_id
                HAVING count(*) > 1) as tbl
            )
        ');
    }
}
