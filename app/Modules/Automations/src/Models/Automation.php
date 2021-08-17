<?php

namespace App\Modules\Automations\src\Models;

use App\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property mixed conditions
 * @property mixed executions
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
    ];

    protected $attributes = [
        'enabled' => false
    ];

    protected $casts = [
        'enabled' => 'boolean'
    ];

    /**
     * @return HasMany
     */
    public function conditions(): HasMany
    {
        return $this->hasMany(Condition::class)->latest();
    }

    /**
     * @return HasMany
     */
    public function executions(): HasMany
    {
        return $this->hasMany(Execution::class)->latest();
    }
}
