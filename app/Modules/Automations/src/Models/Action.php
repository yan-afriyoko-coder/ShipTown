<?php

namespace App\Modules\Automations\src\Models;

use App\BaseModel;

/**
 * @property integer automation_id
 * @property string action_class
 * @property string action_value
 */
class Action extends BaseModel
{
    protected $table = 'modules_automations_actions';

    protected $fillable = [
        'automation_id',
        'priority',
        'action_class',
        'action_value',
    ];
}
