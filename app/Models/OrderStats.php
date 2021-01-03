<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\OrderStats
 *
 * @property int $order_id
 * @property int|null $age_in_days
 * @method static Builder|OrderStats newModelQuery()
 * @method static Builder|OrderStats newQuery()
 * @method static Builder|OrderStats query()
 * @method static Builder|OrderStats whereAgeInDays($value)
 * @method static Builder|OrderStats whereOrderId($value)
 * @mixin Eloquent
 */
class OrderStats extends Model
{
    //
}
