<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ClearEverything extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // just to store it really


//        #SELECT *
//        DELETE
//        FROM guineys_pick_requests WHERE order_product_id IN (
//            SELECT guineys_order_products.id
//
//            FROM guineys_order_products
//
//            WHERE guineys_order_products.order_id IN (
//              SELECT ID
//              FROM guineys_orders
//              WHERE guineys_orders.status_code IN ('packing_warehouse', 'packing_web', 'paid', 'picking', 'processing','ready')
//            )
//        );
//
//
//
//        #SELECT * FROM
//        #DELETE FROM
//        UPDATE
//
//        guineys_order_products
//
//        SET quantity_picked = 0
//
//        WHERE quantity_picked > 0
//        AND guineys_order_products.order_id IN (
//          SELECT ID
//          FROM guineys_orders
//          WHERE guineys_orders.status_code IN ('packing_warehouse', 'packing_web', 'paid', 'picking', 'processing','ready')
//        );
//
//
//        #SELECT *
//        DELETE
//        FROM `guineys_pick_requests`
//        WHERE `quantity_picked` = '0' AND `deleted_at` IS NULL;
//
//
//        #SELECT *
//        DELETE
//        FROM `guineys_picks`
//        WHERE `picked_at` IS NULL AND `deleted_at` IS NULL;
//
//
//        UPDATE guineys_orders
//        SET
//        status_code = 'processing',
//        picked_at = null,
//        packed_at = null,
//        packer_user_id = null
//
//        WHERE `status_code` IN ('packing_warehouse', 'packing_web', 'paid', 'picking', 'processing','ready');
//
//
//        #SELECT *
//        DELETE
//        FROM `guineys_order_shipments`
//        WHERE `shipping_number` IN ('3276000690573','test','123');

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
