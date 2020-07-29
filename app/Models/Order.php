<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;

/**
 * @property string status_code
 */
class Order extends Model
{
    protected $fillable = [
        'order_number',
        'order_placed_at',
        'order_closed_at',
        'status_code',
        'raw_import'
    ];

    protected $casts = [
        'raw_import' => 'array',
    ];

    // we use attributes to set default values
    // we wont use database default values
    // as this is then not populated
    // correctly to events
    protected $attributes = [
        'raw_import' => '{}',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany | OrderProduct
     */
    public function orderProducts()
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function scopeActive($query) {
        return $query->where('status_code', '=', 'processing');
    }
}
