<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ShippingCourier extends Model
{
    protected $fillable = [
        'code',
        'service_provider_class',
    ];

    /**
     * @return mixed
     */
    public static function getSpatieQueryBuilder()
    {
        return QueryBuilder::for(ShippingCourier::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::exact('code'),
            ])
            ->allowedSorts([
                'id',
                'code',
            ]);
    }
}
