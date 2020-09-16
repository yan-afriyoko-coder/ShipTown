<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStatus extends Model
{
    public static $activeOrderStatusList = [
        'processing',
        'paid',
        'picking',
        'packing',
        'packing_warehouse',
    ];

    public static $toFollowStatusList = [
        'unshipped',
        'partially_shipped',
        'holded',
        'missing_item',
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
