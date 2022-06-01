<?php

namespace App\Modules\Automations\src\Models;

use App\BaseModel;
use App\Modules\Automations\src\Abstracts\BaseOrderConditionAbstract;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 *
 * @property integer id
 * @property integer priority
 * @property boolean enabled
 * @property string name
 * @property string event_class
 * @property mixed conditions
 * @property mixed actions
 *
 */
class Automation extends BaseModel
{
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
        'event_class',
        'description'
    ];

    protected $attributes = [
        'enabled' => false
    ];

    protected $casts = [
        'enabled' => 'boolean'
    ];


    /**
     * @param $event
     * @return bool
     */
    public function allConditionsTrue($event): bool
    {
        return $this->conditions
            ->every(function (Condition $condition) use ($event) {
                return $condition->isTrue($event);
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


    public function addConditions($query)
    {
        try {
            $this->conditions
                ->each(function (Condition $condition) use ($query) {
                    /** @var BaseOrderConditionAbstract $c */
                    $c = $condition->condition_class;
                    $c::addQueryScope($query, $condition->condition_value);
                });
        } catch (\Exception $exception) {
            report($exception);
            // we will invalidate query here
            return $query->whereRaw('1=2');
        }

        return $query;
    }
}
