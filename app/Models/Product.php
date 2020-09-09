<?php

namespace App\Models;

use DateTime;
use Hulkur\HasManyKeyBy\HasManyKeyByRelationship;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use phpseclib\Math\BigInteger;

/**
 * @property BigInteger id
 * @property string sku
 * @property string name
 * @property double price
 * @property double sale_price
 * @property DateTime sale_price_start_date
 * @property DateTime sale_price_end_date
 * @property double quantity
 * @property double quantity_reserved
 * @method static updateOrCreate(array $array, array $attributes)
 * @method static whereHasText(string $text)
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

    /**
     * @param \Illuminate\Database\Query\Builder $query
     * @param string $text
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeWhereHasText($query, $text)
    {
        return $query->where('sku', 'like', '%' . $text . '%')
            ->orWhere('name', 'like', '%' . $text . '%')
            ->orWhereHas('aliases', function (Builder $query) use ($text) {
                    return $query->where('alias', '=', $text);
            });
    }

    /**
     * @param \Illuminate\Database\Query\Builder $query
     * @param int $inventory_location_id
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeAddInventorySource($query, $inventory_location_id)
    {
        $source_inventory = Inventory::query()
            ->select([
                'shelve_location as inventory_source_shelf_location',
                'quantity as inventory_source_quantity',
                'product_id as inventory_source_product_id',
                'location_id as inventory_source_location_id',
            ])
            ->where(['location_id'=>$inventory_location_id])
            ->toBase();

        return $query->leftJoinSub($source_inventory, 'inventory_source', function ($join) {
            $join->on('products.id', '=', 'inventory_source_product_id');
        });
    }

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

    public function aliases()
    {
        return $this->hasMany(ProductAlias::class);
    }

    public static function findBySKU(string $sku)
    {
        return static::query()->where('sku', '=', $sku)->first();
    }



    /**
     * @param Builder $query
     * @param string $skuOrAlias
     * @return Builder
     */
    public function scopeSkuOrAlias(Builder $query, string $skuOrAlias)
    {
        return $query->whereHas('aliases', function (Builder $query) use ($skuOrAlias) {
            return $query->where('alias', 'like', '%'.$skuOrAlias.'%');
        });
    }
}
