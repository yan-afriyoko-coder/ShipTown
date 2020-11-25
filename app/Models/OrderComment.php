<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\QueryBuilder\QueryBuilder;

class OrderComment extends Model
{
    protected $fillable = [
        'order_id',
        'user_id',
        'comment'
    ];

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return QueryBuilder
     */
    public static function getSpatieQueryBuilder(): QueryBuilder
    {
        return QueryBuilder::for(OrderComment::class)
            ->allowedFilters([
            ])
            ->allowedIncludes([
            ])
            ->allowedSorts([
            ]);
    }
}
