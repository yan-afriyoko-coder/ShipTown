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
        'name',
        'event_class',
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
