<?php

namespace App\Models;

use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\QueryBuilder\QueryBuilder;

/**
 *  @property int $id
 *  @property int $product_id
 *  @property double $quantity_collected
 *  @property double $quantity_expected
 *  @property double $quantity_required
 *  @property int $user_id
 *  @property Carbon $created_at
 *  @property Carbon $updated_at
 *
 *  @property-read Product $product
 *  @property-read User $user
 */
class DataCollectionRecord extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'product_id',
        'quantity_expected',
        'quantity_collected',
        'user_id'
    ];

    protected $casts = [
        'quantity_collected' => 'double',
        'quantity_expected' => 'double',
        'quantity_required' => 'double',
        'user_id' => 'int',
        'product_id' => 'int'
    ];

    /**
     * @return QueryBuilder
     */
    public static function getSpatieQueryBuilder(): QueryBuilder
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

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
