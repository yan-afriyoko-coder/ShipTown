<?php

namespace App\Modules\Automations\src\Models;

use App\BaseModel;

/**
 * @property integer order_id
 *
 */
class OrderLock extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'modules_automations_order_lock';

    /**
     * @var string[]
     */
    protected $fillable = [
        'order_id',
    ];
}
