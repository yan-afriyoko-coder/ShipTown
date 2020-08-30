<?php

namespace App\Events;

use App\Models\Pick;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PickQuantityRequiredChangedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Pick
     */
    private $pick;

    /**
     * Create a new event instance.
     *
     * @param Pick $pick
     */
    public function __construct(Pick $pick)
    {
        $this->pick = $pick;
    }

    /**
     * @return Pick
     */
    public function getPick()
    {
        return $this->pick;
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
