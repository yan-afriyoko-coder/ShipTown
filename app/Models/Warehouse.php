<?php

namespace App\Models;

use App\BaseModel;
use App\Traits\HasTagsTrait;
use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Warehouse.
 *
 * @property int                $id
 * @property string             $code
 * @property string             $name
 * @property int|null           $address_id
 * @property string|null        $deleted_at
 * @property Carbon|null        $created_at
 * @property Carbon|null        $updated_at
 *
 * @property OrderAddress|null  $address
 *
 * @method static Builder|Warehouse newModelQuery()
 * @method static Builder|Warehouse newQuery()
 * @method static Builder|Warehouse query()
 * @method static Builder|Warehouse whereCode($value)
 * @method static Builder|Warehouse whereCreatedAt($value)
 * @method static Builder|Warehouse whereDeletedAt($value)
 * @method static Builder|Warehouse whereId($value)
 * @method static Builder|Warehouse whereName($value)
 * @method static Builder|Warehouse whereUpdatedAt($value)
 *
 * @method static Builder hasTags($tags)
 * @mixin Eloquent
 */
class Warehouse extends BaseModel
{
    use HasTagsTrait;

    protected $fillable = [
        'code',
        'name',
        'address_id',
    ];

    /**
     * @return BelongsTo
     */
    public function address(): BelongsTo
    {
        return $this->belongsTo(OrderAddress::class);
    }
}
