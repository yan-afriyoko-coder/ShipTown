<?php

namespace App\Models;

use App\Helpers\HasQuantityRequiredSort;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

/**
 *  @property int    $id
 *  @property int    $product_id
 *  @property double $quantity_requested
 *  @property double $quantity_scanned
 *  @property double $quantity_to_scan
 *  @property Carbon $created_at
 *  @property Carbon $updated_at
 *
 *  @property-read Product $product
 */
class DataCollectionRecord extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'data_collection_id',
        'product_id',
        'quantity_requested',
        'quantity_scanned',
    ];

    protected $casts = [
        'product_id'         => 'int',
        'quantity_requested' => 'double',
        'quantity_scanned'   => 'double',
        'quantity_to_scan'   => 'double',
    ];

    /**
     * @return QueryBuilder
     */
    public static function getSpatieQueryBuilder(): QueryBuilder
    {
        $allowedSort = AllowedSort::custom('has_quantity_required', new HasQuantityRequiredSort());

        return QueryBuilder::for(DataCollectionRecord::class)
            ->allowedFilters([])
            ->allowedSorts([
                'id',
                'quantity_requested',
                'quantity_scanned',
                'quantity_to_scan',
                'updated_at',
                'created_at',
            ])
            ->allowedIncludes([
                'product',
                'product.inventory',
                'product.user_inventory',
            ])
            ->defaultSort($allowedSort, '-updated_at');
    }

    /**
     * @return BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
