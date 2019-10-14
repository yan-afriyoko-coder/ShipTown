<?php

namespace App\Models;

use App\Events\EventTypes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'order_json'
    ];

    protected $casts = [
        'order_json' => 'array'
    ];

    protected static function boot()
    {
        parent::boot();

        self::created(function($model) {
            event(EventTypes::ORDER_CREATED, new EventTypes($model));
        });

        self::updating(function($model) {
            event(EventTypes::ORDER_UPDATED, new EventTypes([
                "original" => $model->getOriginal(),
                "new" => $model->getAttributes()
            ]));
        });
    }
}
