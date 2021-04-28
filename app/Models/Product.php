<?php

namespace App\Models;

use App\Events\Product\TagAttachedEvent;
use App\Traits\HasTagsTrait;
use App\Traits\LogsActivityTrait;
use Barryvdh\LaravelIdeHelper\Eloquent;
use Hulkur\HasManyKeyBy\HasManyKeyByRelationship;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\Models\Activity;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\Tags\Tag;

/**
 * App\Models\Product
 *
 * @property int $id
 * @property string $sku
 * @property string $name
 * @property string $price
 * @property string $sale_price
 * @property Carbon $sale_price_start_date
 * @property Carbon $sale_price_end_date
 * @property string $quantity
 * @property string $quantity_reserved
 * @property Collection|Tag[] $tags
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read Collection|ProductAlias[] $aliases
 * @property-read int|null $aliases_count
 * @property-read mixed $quantity_available
 * @property-read Collection|Inventory[] $inventory
 * @property-read int|null $inventory_count
 * @property-read DatabaseNotificationCollection|DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read int|null $tags_count
 * @method static Builder|Product addInventorySource($inventory_location_id)
 * @method static Builder|Product newModelQuery()
 * @method static Builder|Product newQuery()
 * @method static Builder|Product onlyTrashed()
 * @method static Builder|Product query()
 * @method static Builder|Product skuOrAlias($skuOrAlias)
 * @method static Builder|Product whereCreatedAt($value)
 * @method static Builder|Product whereDeletedAt($value)
 * @method static Builder|Product whereHasText($text)
 * @method static Builder|Product whereId($value)
 * @method static Builder|Product whereName($value)
 * @method static Builder|Product wherePrice($value)
 * @method static Builder|Product whereQuantity($value)
 * @method static Builder|Product whereQuantityReserved($value)
 * @method static Builder|Product whereSalePrice($value)
 * @method static Builder|Product whereSalePriceEndDate($value)
 * @method static Builder|Product whereSalePriceStartDate($value)
 * @method static Builder|Product whereSku($value)
 * @method static Builder|Product whereUpdatedAt($value)
 * @method static Builder|Product withTrashed()
 * @method static Builder|Product withoutTrashed()
 * @method static Builder|Product withAllTags($tags, $type = null)
 * @method static Builder|Product withAllTagsOfAnyType($tags)
 * @method static Builder|Product withAnyTags($tags, $type = null)
 * @method static Builder|Product withAnyTagsOfAnyType($tags)
 * @mixin Eloquent
 * @method static Builder|Product withoutAllTags($tags, $type = null)
 * @property-read Collection|ProductPrice[] $prices
 * @property-read int|null $prices_count
 */
class Product extends Model
{
    use LogsActivityTrait, HasTagsTrait, SoftDeletes;
    use Notifiable, HasManyKeyByRelationship;

    protected static $logAttributes = [
        'quantity',
        'quantity_reserved'
    ];

    protected $fillable = [
        "sku",
        "name",
        "price",
        "sale_price",
        "sale_price_start_date",
        "sale_price_end_date",
        "quantity_reserved",
        "quantity"
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

    protected $casts = [
        "quantity" => 0,
        'quantity_reserved' => 0
    ];


    /**
     * @return QueryBuilder
     */
    public static function getSpatieQueryBuilder(): QueryBuilder
    {
        return QueryBuilder::for(Product::class)
            ->allowedFilters([
                AllowedFilter::scope('search', 'whereHasText'),
                AllowedFilter::exact('id'),
                AllowedFilter::exact('sku'),
                AllowedFilter::exact('name'),
                AllowedFilter::exact('price'),
                AllowedFilter::scope('inventory_source_location_id', 'addInventorySource')->ignore(['', null]),

                AllowedFilter::scope('has_tags', 'withAllTags'),
                AllowedFilter::scope('without_tags', 'withoutAllTags'),
            ])
            ->allowedSorts([
                'id',
                'sku',
                'name',
                'price',
                'quantity'
            ])
            ->allowedIncludes([
                'inventory',
                'aliases',
                'tags'
            ]);
    }

    /**
     * @param float $quantity
     * @return $this
     */
    public function reserveStock(float $quantity): Product
    {
        $this->quantity_reserved += $quantity;
        $this->save();
        return $this;
    }

    /**
     * @param $tag
     */
    protected function onTagAttached($tag)
    {
        TagAttachedEvent::dispatch($this, $tag);
    }

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

    /**
     * @param Inventory $inventory
     * @param $product
     */
    public function recalculateQuantityTotals(): void
    {
        $this->quantity = Inventory::where(['product_id' => $this->id])
            ->where('quantity', '!=', 0)
            ->sum('quantity');
        $this->quantity_reserved = Inventory::where(['product_id' => $this->id])
            ->where('quantity_reserved', '!=', 0)
            ->sum('quantity_reserved');
        $this->save();
    }

    /**
     * @return HasMany
     */
    public function inventory(): HasMany
    {
        return $this->hasMany(Inventory::class)
            ->keyBy('location_id');
    }

    /**
     * @return mixed|ProductPrice
     */
    public function prices()
    {
        return $this->hasMany(ProductPrice::class)
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

    /**
     * @return bool
     */
    public function isOutOfStock(): bool
    {
        return $this->quantity_available <= 0;
    }

    /**
     * @return bool
     */
    public function isInStock(): bool
    {
        return $this->quantity_available > 0;
    }
}
