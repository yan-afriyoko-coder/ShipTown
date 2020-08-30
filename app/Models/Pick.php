<?php

namespace App\Models;

use App\User;
use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

/**
 * @property float quantity_required
 * @property DateTime|null picked_at
 * @property bool is_picked
 */
class Pick extends Model
{
    use SoftDeletes;

    /**
     * @var string[]
     */
    protected $fillable = [
        'product_id',
        'sku_ordered',
        'name_ordered',
        'quantity_required',
        'picker_user_id',
        'picked_at'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
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
}
