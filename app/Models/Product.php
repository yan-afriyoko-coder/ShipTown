<?php

namespace App\Models;
;

use DateTime;
use Hulkur\HasManyKeyBy\HasManyKeyByRelationship;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

/**
 * @property string sku
 * @property string name
 * @property double price
 * @property double sale_price
 * @property DateTime sale_price_start_date
 * @property DateTime sale_price_end_date
 * @property double quantity
 * @property double quantity_reserved
 */
class Product extends Model
{
    use SoftDeletes;
    use Notifiable, HasManyKeyByRelationship;

    protected $fillable = [
        "sku",
        "name",
        "price",
        "sale_price",
        "sale_price_start_date",
        "sale_price_end_date",
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

    public function inventory()
    {
        return $this->hasMany(Inventory::class)
            ->keyBy('location_id');
    }

    public static function findBySKU(string $sku)
    {
        return static::query()->where('sku', '=', $sku)->first();
    }
}
