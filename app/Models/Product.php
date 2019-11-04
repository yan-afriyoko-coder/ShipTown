<?php

namespace App\Models;

use App\Events\EventTypes;
use App\Scopes\AuthenticatedUserScope;
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

    protected $appends = [
        "quantity_available"
    ];

    protected $attributes = [
        'quantity_reserved' => 0,
    ];

    public function getQuantityAvailableAttribute()
    {
        $quantity_available = $this->quantity - $this->quantity_reserved;

        if ($quantity_available<0) {
            return 0;
        }

        return $quantity_available;
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->user_id = auth()->id();
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new AuthenticatedUserScope());

        self::created(function($model) {
            event(EventTypes::PRODUCT_CREATED, new EventTypes($model));
        });

        self::updating(function($model) {
            event(EventTypes::PRODUCT_UPDATED, new EventTypes($model));
        });
    }
}
