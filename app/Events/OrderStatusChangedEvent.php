<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusChangedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Order
     */
    public $order;

    /**
     * Create a new event instance.
     *
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * @return Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @return array|mixed
     */
    public function getOldStatusCode()
    {
        return $this->order->getOriginal('status_code');
    }

    /**
     * @return array|mixed
     */
    public function getNewStatusCode()
    {
        return $this->order->status_code;
    }

    /**
     * @param $expected
     * @return bool
     */
    public function isStatusCode($expected)
    {
        return $this->getNewStatusCode() === $expected;
    }

    /**
     * @param $expected
     * @return bool
     */
    public function wasStatusCode($expected)
    {
        return $this->getOldStatusCode() === $expected;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
