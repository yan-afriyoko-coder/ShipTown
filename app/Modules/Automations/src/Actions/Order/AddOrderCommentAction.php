<?php

namespace App\Modules\Automations\src\Actions\Order;

use App\Events\Order\ActiveOrderCheckEvent;
use App\Events\Order\OrderCreatedEvent;
use App\Events\Order\OrderUpdatedEvent;
use App\Models\OrderComment;
use Log;

class AddOrderCommentAction
{
    /**
     * @var ActiveOrderCheckEvent|OrderCreatedEvent|OrderUpdatedEvent
     */
    private $event;

    public function __construct($event)
    {
        $this->event = $event;
    }

    /**
     * @param $value
     */
    public function handle($value)
    {
        Log::debug('Adding order comment', [
            'order_number' => $this->event->order->order_number,
            'class' => self::class,
            'comment' => $value,
        ]);

        $comment = new OrderComment();
        $comment->comment = $value;
        $comment->order_id = $this->event->order->getKey();
        $comment->save();
    }
}
