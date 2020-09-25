<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStatus extends Model
{
    public static $activeOrderStatusList = [
        'processing',
        'paid',
        'picking',
        'picking_web',
        'picking_warehouse',
        'packing',
        'packing_web',
        'packing_warehouse',
        'ready',
        'for_later',
    ];

    public static $toFollowStatusList = [
        'unshipped',
        'partially_shipped',
        'holded',
        'missing_item',
        'auto_missing_item',
    ];

    public static function getActiveStatusCodesList()
    {
        return self::$activeOrderStatusList;
    }
    public static function getToFollowStatusList()
    {
        return self::$toFollowStatusList;
    }
}
