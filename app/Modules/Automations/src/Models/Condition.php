<?php

namespace App\Modules\Automations\src\Models;

use App\BaseModel;

/**
 * @property integer automation_id
 * @property string validation_class
 * @property string condition_value
 */
class Condition extends BaseModel
{
    protected $table = 'modules_automations_conditions';

    protected $fillable = [
        'automation_id',
        'validation_class',
        'condition_value',
    ];
}
