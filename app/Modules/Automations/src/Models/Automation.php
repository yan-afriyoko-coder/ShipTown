<?php

namespace App\Modules\Automations\src\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\BaseModel;
use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 *
 * @property integer id
 * @property integer priority
 * @property boolean enabled
 * @property string name
 * @property mixed conditions
 * @property mixed actions
 *
 */
class Automation extends BaseModel
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'modules_automations';

    /**
     * @var string[]
     */
    protected $fillable = [
        'priority',
        'enabled',
        'name',
        'description'
    ];

    protected $attributes = [
        'enabled' => false
    ];

    protected $casts = [
        'enabled' => 'boolean'
    ];

    /**
     * @return Builder
     */
    public static function enabled(): Builder
    {
        return self::query()->where(['enabled' => true]);
    }


    /**
     * @param Order $order
     * @return bool
     */
    public function allConditionsTrue(Order $order): bool
    {
        return $this->conditions
            ->every(function (Condition $condition) use ($order) {
                return $condition->isTrue($order);
            });
    }

    /**
     * @return HasMany
     */
    public function conditions(): HasMany
    {
        return $this->hasMany(Condition::class)->orderBy('id');
    }

    /**
     * @return HasMany
     */
    public function actions(): HasMany
    {
        return $this->hasMany(Action::class)->orderBy('priority');
    }
}
