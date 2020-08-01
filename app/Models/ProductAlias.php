<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAlias extends Model
{
    protected $table = 'products_aliases';

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
