<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStatus extends Model
{
    public static $activeOrderStatusList = [
        'paid',
        'picking',
        'picking_web',
        'picking_warehouse',
        'packing',
        'packing_web',
        'packing_warehouse',
        'for_later',
    ];

    public static $toFollowStatusList = [
        'processing',
        'unshipped',
        'partially_shipped',
        'holded',
        'on_hold',
        'missing_item',
        'auto_missing_item',
    ];

    public static $completedStatusCodeList = [
        'cancelled',
        'canceled',
        'closed',
        'complete',
        'complete_imported_to_rms',
        'ready'
    ];

    public static function getActiveStatusCodesList()
    {
        return self::$activeOrderStatusList;
    }

    public static function getToFollowStatusList()
    {
        return self::$toFollowStatusList;
    }

    public static function getCompletedStatusCodeList()
    {
        return self::$completedStatusCodeList;
    }

    public static function isActive(string $status_code)
    {
        return array_search($status_code, self::getActiveStatusCodesList()) != false;
    }

    public static function isToFollow(string $status_code)
    {
        return array_search($status_code, self::getToFollowStatusList()) != false;
    }

    public static function isComplete(string $status_code)
    {
        return array_search($status_code, self::getCompletedStatusCodeList()) != false;
    }
}
