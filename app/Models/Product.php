<?php

namespace App\Models;

use App\Events\EventTypes;
use App\Scopes\AuthenticatedUserScope;
use App\User;
use Hulkur\HasManyKeyBy\HasManyKeyByRelationship;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Product extends Model
{
    use Notifiable, HasManyKeyByRelationship;

    protected $fillable = [
        "user_id",
        "sku",
        "name",
        "price",
        "sale_price",
        "sale_price_start_date",
        "sale_price_end_date",
        "quantity",
        "quantity_reserved"
    ];

    protected $appends = [
        "quantity_available"
    ];

    // we use attributes to set default values
    // we wont use database default values
    // as this is then not populated
    // correctly to events
    protected $attributes = [
        'name' => '',
        'price' => 0,
        "sale_price" => 0,
        "sale_price_start_date" => '2001-01-01 00:00:00',
        "sale_price_end_date" => '2001-01-01 00:00:00',
        "quantity" => 0,
        'quantity_reserved' => 0
    ];

    protected $dates = [
        'sale_price_start_date',
        'sale_price_end_date'
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function inventory()
    {
        return $this->hasMany(Inventory::class)
            ->keyBy('location_id');
    }
}
