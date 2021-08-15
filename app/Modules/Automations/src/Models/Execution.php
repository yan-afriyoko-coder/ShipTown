<?php

namespace App\Modules\Automations\src\Models;

use App\BaseModel;

/**
 * @property string execution_class
 * @property string execution_value
 */
class Execution extends BaseModel
{
    protected $table = 'modules_automations_executions';

    protected $fillable = [
        'automation_id',
        'priority',
        'execution_class',
        'execution_value',
    ];
}
