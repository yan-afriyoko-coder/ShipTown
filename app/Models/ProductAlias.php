<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ProductAlias extends Model
{
    protected $table = 'products_aliases';

    protected $fillable = [
        'product_id',
        'alias'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @return QueryBuilder
     */
    public static function getSpatieQueryBuilder(): QueryBuilder
    {
        return QueryBuilder::for(ProductAlias::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::exact('product_id'),
                AllowedFilter::partial('alias'),
            ])
            ->allowedIncludes([
            ])
            ->allowedSorts([
            ]);
    }
}
