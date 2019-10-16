<?php

namespace App\Models;

use App\Events\EventTypes;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        "sku",
        "name",
        "price",
        "sale_price",
        "sale_price_start_date",
        "sale_price_end_date",
        "quantity",
    ];

    protected $attributes = [
        'quantity_reserved' => 0,
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->user_id = auth()->id();
    }

    protected static function boot()
    {
        parent::boot();

        self::created(function($model) {
            event(EventTypes::PRODUCT_CREATED, new EventTypes($model));
        });

        self::updating(function($model) {
            $model_history = [
                "original" => $model->getOriginal(),
                "new" => $model->getAttributes()
            ];

            event(EventTypes::PRODUCT_UPDATED, new EventTypes($model_history));
        });
    }
}
