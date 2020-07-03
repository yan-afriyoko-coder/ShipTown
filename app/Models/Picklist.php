<?php

namespace App\Models;

use Hulkur\HasManyKeyBy\HasManyKeyByRelationship;
use Illuminate\Database\Eloquent\Model;

class Picklist extends Model
{
    use HasManyKeyByRelationship;

    protected $fillable = [
        'product_id',
        'location_id',
        'shelve_location',
        'quantity_to_pick',
    ];

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
