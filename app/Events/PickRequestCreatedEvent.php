<?php

namespace App\Events;

use App\Models\PickRequest;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PickRequestCreatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var PickRequest
     */
    private $pickRequest;

    /**
     * Create a new event instance.
     *
     * @param PickRequest $pickRequest
     */
    public function __construct(PickRequest $pickRequest)
    {
        $this->pickRequest = $pickRequest;
    }

    /**
     * @return PickRequest
     */
    public function getPickRequest()
    {
        return $this->pickRequest;
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
