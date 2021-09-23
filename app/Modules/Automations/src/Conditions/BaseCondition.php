<?php

namespace App\Modules\Automations\src\Conditions;

/**
 *
 */
abstract class BaseCondition
{
    /**
     * @var mixed
     */
    protected $event;

    /**
     * @param $event
     */
    public function __construct($event)
    {
        $this->event = $event;
    }
}
