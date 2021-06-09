<?php

namespace App\Models;

use App\BaseModel;
use App\Traits\HasTagsTrait;
use App\Traits\LogsActivityTrait;
use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * App\Models\Order
 *
 * @property int $id
 * @property int|null $shipping_address_id
 * @property string $order_number
 * @property string $status_code
 * @property boolean $is_active
 * @property string $total
 * @property string $total_paid
 * @property string|null $order_placed_at
 * @property string|null $order_closed_at
 * @property int $product_line_count
 * @property string|null $picked_at
 * @property string|null $packed_at
 * @property int|null $packer_user_id
 * @property string $total_quantity_ordered
 * @property array $raw_import
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Activity[] $activities
 * @property-read int|null $activities_count
 * @property mixed $is_packed
 * @property-read mixed $is_paid
 * @property mixed $is_picked
 * @property-read Collection|OrderComment[] $orderComments
 * @property-read int|null $order_comments_count
 * @property-read Collection|OrderProduct[] $orderProducts
 * @property-read int|null $order_products_count
 * @property-read Collection|OrderShipment[] $orderShipments
 * @property-read int|null $order_shipments_count
 * @property-read User|null $packer
 * @property-read Collection|Packlist[] $packlist
 * @property-read int|null $packlist_count
 * @property-read OrderAddress|null $shippingAddress
 * @property-read OrderStats|null $stats
 * @property-read OrderStatus $order_status
 * @property-read boolean isPaid
 * @property-read boolean isNotPaid
 * @method static Builder|Order active()
 * @method static Builder|Order addInventorySource($inventory_location_id)
 * @method static Builder|Order hasPacker($expected)
 * @method static Builder|Order isPacked($value)
 * @method static Builder|Order isPacking($is_packing)
 * @method static Builder|Order isPicked($expected)
 * @method static Builder|Order newModelQuery()
 * @method static Builder|Order newQuery()
 * @method static Builder|Order query()
 * @method static Builder|Order whereActive()
 * @method static Builder|Order whereCreatedAt($value)
 * @method static Builder|Order whereDeletedAt($value)
 * @method static Builder|Order whereHasText($text)
 * @method static Builder|Order whereId($value)
 * @method static Builder|Order whereIsNotPicked()
 * @method static Builder|Order whereIsPicked()
 * @method static Builder|Order whereOrderClosedAt($value)
 * @method static Builder|Order whereOrderNumber($value)
 * @method static Builder|Order whereOrderPlacedAt($value)
 * @method static Builder|Order wherePackedAt($value)
 * @method static Builder|Order wherePackerUserId($value)
 * @method static Builder|Order wherePickedAt($value)
 * @method static Builder|Order whereProductLineCount($value)
 * @method static Builder|Order whereRawImport($value)
 * @method static Builder|Order whereShippingAddressId($value)
 * @method static Builder|Order whereStatusCode($value)
 * @method static Builder|Order whereTotal($value)
 * @method static Builder|Order whereTotalPaid($value)
 * @method static Builder|Order whereTotalQuantityOrdered($value)
 * @method static Builder|Order whereUpdatedAt($value)
 * @method static Builder|Order whereIsActive()
 * @property-read int $age_in_days
 * @property OrderStatus orderStatus
 */
class Order extends BaseModel
{
    use LogsActivityTrait, HasTagsTrait;

//    protected $touches = ['orderProducts'];

    protected $fillable = [
        'order_number',
        'picked_at',
        'shipping_number',
        'is_active',
        'shipping_address_id',
        'is_packed',
        'order_placed_at',
        'order_closed_at',
        'status_code',
        'packer_user_id',
        'raw_import',
        'total',
        'total_paid',
    ];

    /**
     * @var array|string[]
     */
    protected static array $logAttributes = [
        'status_code',
        'packer_user_id'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'raw_import' => 'array',
    ];

    // we use attributes to set default values
    // we wont use database default values
    // as this is then not populated
    // correctly to events
    protected $attributes = [
        'status_code' => 'processing',
        'raw_import' => '{}',
    ];

    protected $appends = [
        'is_picked',
        'is_packed',
        'age_in_days',
    ];

    protected $dates = [
        'order_closed_at'
    ];

    /**
     * @return $this
     */
    public function recalculateTotals(): Order
    {
        $this->total_quantity_ordered = $this->orderProducts()->sum('quantity_ordered');
        $this->product_line_count = $this->orderProducts()->count('id');
        return $this;
    }

    /**
     * @return OrderStatus
     */
    public function getOrderStatusAttribute(): OrderStatus
    {
        return $this->orderStatus();
    }

    /**
     * @return OrderStatus
     */
    public function orderStatus(): OrderStatus
    {
        return OrderStatus::firstOrCreate([
            'name' => $this->status_code,
            'code'=> $this->status_code
        ]);
    }

    /**
     * @return OrderStatus
     */
    public function getPreviousOrderStatus(): OrderStatus
    {
        return OrderStatus::whereCode($this->getOriginal('status_code'));
    }

    public function isOpen(): bool
    {
        return $this->order_closed_at === null;
    }

    /**
     * @return bool
     */
    public function isClosed(): bool
    {
        return ! $this->isOpen();
    }

    /**
     * @param $query
     * @param $text
     * @return Builder
     */
    public function scopeWhereHasText($query, $text)
    {
        return $query->where('order_number', 'like', '%' . $text . '%')
            ->orWhere('status_code', '=', $text)
            ->orWhereHas('orderShipments', function ($query) use ($text) {
                return $query->where('shipping_number', 'like', '%'. $text . '%');
            });
    }

    /**
     * @return int
     */
    public function getAgeInDaysAttribute()
    {
        return Carbon::now()->ceilDay()->diffInDays($this->order_placed_at);
    }

    public function scopeWhereIsActive($query)
    {
        return $this->scopeIsActive($query);
    }

    public function scopeWhereActive($query)
    {
        return $this->scopeIsActive($query);
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeIsActive(Builder $query): Builder
    {
        return $query->whereNotIn('status_code', OrderStatus::getClosedStatuses())
            ->whereNotIn('status_code', OrderStatus::getToFollowStatusList());
    }

    /**
     * @param Builder $query
     * @param string $fromDateTime
     * @param string $toDateTime
     * @return Builder
     */
    public function scopePackedBetween(Builder $query, string $fromDateTime, string $toDateTime): Builder
    {
        try {
            $dates = [
                Carbon::parse($fromDateTime),
                Carbon::parse($toDateTime),
            ];
        } catch (\Exception $exception) {
            $dates = [
                Carbon::today(),
                Carbon::now(),
            ];
        }

        return $query->whereBetween('packed_at', $dates);
    }

    /**
     * @param QueryBuilder $query
     * @param int $age
     * @return Builder|QueryBuilder
     */
    public function scopeWhereAgeInDays($query, $age)
    {
        return $query->orWhereBetween('order_placed_at', [
            Carbon::now()->subDays($age)->startOfDay(),
            Carbon::now()->subDays($age)->endOfDay(),
        ]);
    }

    /**
     * @param Builder $query
     * @param int $inventory_location_id
     * @return Builder
     */
    public function scopeAddInventorySource($query, $inventory_location_id)
    {
        $source_inventory = OrderProduct::query()
            ->select([
                'order_id as order_id',
                DB::raw('min(shelve_location) as min_shelf_location'),
                DB::raw('max(shelve_location) as max_shelf_location'),
            ])
            ->leftJoin('inventory', function ($join) use ($inventory_location_id) {
                $join->on('order_products.product_id', '=', 'inventory.product_id');
                $join->on('inventory.location_id', '=', DB::raw($inventory_location_id));
            })
            ->groupBy('order_products.order_id')
            ->toBase();

        return $query->leftJoinSub($source_inventory, 'inventory_source', function ($join) {
            $join->on('orders.id', '=', 'inventory_source.order_id');
        });
    }

    public function getIsPaidAttribute()
    {
        return ($this->total > 0) && ($this->total === $this->total_paid);
    }

    public function getIsNotPaidAttribute(): bool
    {
        return ! $this->isPaid;
    }

    /**
     * @param $expected
     * @return bool
     */
    public function isNotStatusCode($expected)
    {
        return !$this->isStatusCode($expected);
    }

    /**
     * @param $expected
     * @return bool
     */
    public function isStatusCode($expected)
    {
        return $this->getAttribute('status_code') === $expected;
    }


    /**
     * @param array $statusCodes
     * @return bool
     */
    public function isStatusCodeNotIn(array $statusCodes)
    {
        return !$this->isStatusCodeIn($statusCodes);
    }

    /**
     * @param array $statusCodes
     * @return bool
     */
    public function isStatusCodeIn(array $statusCodes)
    {
        $statusCode = $this->getAttribute('status_code');

        return array_search($statusCode, $statusCodes) > -1;
    }

    /**
     * @param $query
     * @param bool $expected
     * @return self
     */
    public function scopeHasPacker($query, bool $expected)
    {
        if ($expected === false) {
            return $query->whereNull('packer_user_id');
        }

        return $query->whereNotNull('packer_user_id');
    }

    /**
     * @param $query
     * @param bool $expected
     * @return self
     */
    public function scopeIsPicked($query, bool $expected)
    {
        if ($expected === true) {
            return $query->whereIsPicked();
        }

        return $query->whereIsNotPicked();
    }

    /**
     * @param $query
     * @return self
     */
    public function scopeIsPacking($query, $is_packing)
    {
        if ($is_packing) {
            return $query->whereNotNull('packer_user_id');
        }

        return $query->whereNull('packer_user_id');
    }

    /**
     * @param $query
     * @return self
     */
    public function scopeWhereIsPicked($query)
    {
        return $query->whereNotNull('picked_at');
    }

    /**
     * @param $query
     * @return self
     */
    public function scopeWhereIsNotPicked($query)
    {
        return $query->whereNull('picked_at');
    }

    public function scopeIsPacked($query)
    {
        return $query->whereNull('packed_at');
    }

    public function getIsPackedAttribute()
    {
        return $this->packed_at !== null;
    }

    public function setIsPackedAttribute($value)
    {
        $this->packed_at = $value ? now() : null;
    }

    public function getIsPickedAttribute()
    {
        return $this->picked_at !== null;
    }

    public function setIsPickedAttribute($value)
    {
        $this->picked_at = $value ? now() : null;
    }

    public function scopeActive($query)
    {
        return $query->where('status_code', '=', 'processing');
    }

    /**
     * @return HasMany | OrderProduct
     */
    public function orderProducts()
    {
        return $this->hasMany(OrderProduct::class);
    }

    /**
     * @return HasMany | Packlist
     */
    public function packlist()
    {
        return $this->hasMany(Packlist::class);
    }

    /**
     * @return BelongsTo | OrderAddress
     */
    public function shippingAddress()
    {
        return $this->belongsTo(OrderAddress::class);
    }

    /**
     * @return BelongsTo | User
     */
    public function packer()
    {
        return $this->belongsTo(User::class, 'packer_user_id');
    }

    /**
     * @return HasMany | OrderShipment
     */
    public function orderShipments()
    {
        return $this->hasMany(OrderShipment::class)->latest();
    }
    /**
     * @return HasOne
     */
    public function stats()
    {
        return $this->hasOne(OrderStats::class);
    }

    /**
     * @return HasMany | OrderComment
     */
    public function orderComments()
    {
        return $this->hasMany(OrderComment::class)->latest();
    }

    /**
     * @return QueryBuilder
     */
    public static function getSpatieQueryBuilder(): QueryBuilder
    {
        return QueryBuilder::for(Order::class)
            ->allowedFilters([
                AllowedFilter::scope('search', 'whereHasText')->ignore([null, '']),


                AllowedFilter::exact('status', 'status_code'),
                AllowedFilter::exact('order_number')->ignore([null, '']),
                AllowedFilter::exact('packer_user_id'),

                AllowedFilter::scope('age_in_days', 'whereAgeInDays')->ignore([null,'']),
                AllowedFilter::scope('is_picked'),
                AllowedFilter::scope('is_packed'),
                AllowedFilter::scope('is_packing'),
                AllowedFilter::scope('is_active'),
                AllowedFilter::scope('packed_between'),

                AllowedFilter::scope('has_packer'),

                AllowedFilter::scope('inventory_source_location_id', 'addInventorySource')->ignore([null, '']),

                AllowedFilter::scope('has_tags', 'withAllTags'),
                AllowedFilter::scope('without_tags', 'withoutAllTags'),
            ])
            ->allowedIncludes([
                'activities',
                'activities.causer',
                'stats',
                'shipping_address',
                'order_shipments',
                'order_products',
                'order_products.product',
                'order_products.product.aliases',
                'packer',
                'order_comments',
                'order_comments.user',
            ])
            ->allowedSorts([
                'updated_at',
                'product_line_count',
                'total_quantity_ordered',
                'order_placed_at',
                'packed_at',
                'order_closed_at',
                'min_shelf_location',
            ]);
    }
}
