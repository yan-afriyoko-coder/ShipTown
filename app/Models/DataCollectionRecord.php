<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\QueryBuilder\QueryBuilder;

/**
 *
 */
class DataCollectionRecord extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'product_id',
        'quantity',
        'user_id'
    ];

    public static function getSpatieQueryBuilder()
    {
        return QueryBuilder::for(DataCollectionRecord::class)
            ->allowedFilters([])
            ->allowedSorts([
                'id',
            ])
            ->allowedIncludes([
                'product',
            ]);
    }

    /**
     * @return BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
