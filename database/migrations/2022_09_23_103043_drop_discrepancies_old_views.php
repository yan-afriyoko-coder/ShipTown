<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DropDiscrepanciesOldViews extends Migration
{
    public function up()
    {
        DB::statement('DROP VIEW IF EXISTS `modules_api2cart_product_quantity_discrepancies`');
        DB::statement('DROP VIEW IF EXISTS `modules_api2cart_product_pricing_discrepancies`');
    }
}
