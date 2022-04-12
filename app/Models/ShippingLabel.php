<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\QueryBuilder;

class ShippingLabel extends Model
{
    public static function getSpatieQueryBuilder(): QueryBuilder
    {
        return QueryBuilder::for(ShippingLabel::class);
    }
}
