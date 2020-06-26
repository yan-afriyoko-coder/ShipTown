<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Picklist extends Model
{
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

}
