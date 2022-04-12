<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * @property int    id
 * @property string code
 * @property string service_provider_class
 */
class ShippingService extends Model
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
        return QueryBuilder::for(ShippingService::class)
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
