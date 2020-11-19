<?php

namespace App\Models;

use App\User;
use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

/**
 * @property string sku_ordered
 * @property string name_ordered
 * @property float quantity_required
 * @property DateTime|null picked_at
 * @property bool is_picked
 * @property User user
 * @method static self whereHasQuantityRequired()
 */
class Pick extends Model
{
    use SoftDeletes;

    /**
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'product_id',
        'sku_ordered',
        'name_ordered',
        'quantity_picked',
        'quantity_skipped_picking',

        'quantity_required',
        'picker_user_id',
        'picked_at'
    ];

    /**
     * @return BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'picker_user_id');
    }

    public function scopeWhereHasQuantityRequired($query)
    {
        return $query->where('quantity_required', '>', 0);
    }
    /**
     * @param Builder $query
     * @param boolean $in_stock
     * @return mixed
     */
    public function scopeWhereInStock($query, $in_stock)
    {
        if (!$in_stock) {
            return $query;
        }

        return $query->where('inventory_source.inventory_source_quantity', '>', 0);
    }

    /**
     * @param Builder $query
     * @param string $currentLocation
     * @return Builder
     */
    public function scopeMinimumShelfLocation($query, $currentLocation)
    {
        return $query->where('inventory_source.inventory_source_shelf_location', '>=', $currentLocation);
    }

    /**
     * @param Builder $query
     * @param int $inventory_location_id
     * @return Builder
     */
    public function scopeAddInventorySource($query, $inventory_location_id)
    {
        $source_inventory = Inventory::query()
            ->select([
                'shelve_location as inventory_source_shelf_location',
                'quantity as inventory_source_quantity',
                'product_id as inventory_source_product_id',
            ])
            ->where(['location_id'=>$inventory_location_id])
            ->toBase();

        return $query->leftJoinSub($source_inventory, 'inventory_source', function ($join) {
            $join->on('picks.product_id', '=', 'inventory_source_product_id');
        });
    }

    /**
     * @param $query
     * @return Builder
     */
    public function scopeWhereNotPicked($query)
    {
        return $query->whereNull('picked_at');
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeWherePicked(Builder $query)
    {
        return $query->whereNotNull('picked_at');
    }

    /**
     * @param User $picker
     * @param float $quantity_picked
     */
    public function pick(User $picker, float $quantity_picked)
    {
        if ($quantity_picked == 0) {
            $this->update([
                'picker_user_id' => null,
                'picked_at' => null
            ]);

            return;
        }

        // we do it in 2 separate calls so Picked & QuantityRequiredChanged event are dispatched correctly
        $this->update([
            'quantity_required' => $quantity_picked
        ]);

        $this->update([
            'picker_user_id' => $picker->getKey(),
            'picked_at' => now()
        ]);
    }

    /**
     * @param $name
     * @return bool
     */
    public function isAttributeValueChanged($name)
    {
        return $this->getAttribute($name) != $this->getOriginal($name);
    }

    /**
     * @return bool
     */
    public function getIsPickedAttribute()
    {
        return $this->picked_at != null;
    }

    /**
     * @return HasMany
     */
    public function pickRequests()
    {
        return $this->hasMany(PickRequest::class, 'pick_id');
    }
}
