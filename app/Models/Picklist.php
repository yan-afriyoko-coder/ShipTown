<?php

namespace App\Models;

use Hulkur\HasManyKeyBy\HasManyKeyByRelationship;
use Illuminate\Database\Eloquent\Model;

class Picklist extends Model
{
    use HasManyKeyByRelationship;

    protected $fillable = [
        'order_id',
        'order_product_id',
        'product_id',
        'location_id',
        'sku_ordered',
        'name_ordered',
        'quantity_requested',
        'quantity_picked',
        'picked_at',
    ];

    public static function addPick($params) {
        return self::query()->create($params);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function inventory()
    {
        return $this->hasMany(
            Inventory::class,
            'product_id',
            'product_id'
        )->keyBy('location_id');
    }
}
