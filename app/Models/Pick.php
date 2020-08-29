<?php

namespace App\Models;

use App\User;
use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

/**
 * @property DateTime|null picked_at
 */
class Pick extends Model
{
    protected $fillable = [
        'product_id',
        'sku_ordered',
        'name_ordered',
        'quantity_required',
        'picker_user_id',
        'picked_at'
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('notPickedOnly', function ($builder) {
            $builder->whereNull('picked_at');
        });
    }

    public function scopePicked(Builder $query)
    {
        return $query->whereNull('picked_at');
    }

    public function pickBy(User $picker)
    {
        $this->update([
            'picker_user_id' => $picker->getKey(),
            'picked_at' => now()
        ]);
    }

    public function wasJustPicked()
    {
        return isset($this->picked_at) && $this->getOriginal('picked_at') === null;
    }
}
