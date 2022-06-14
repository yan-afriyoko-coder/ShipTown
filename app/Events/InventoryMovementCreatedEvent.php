<?php

namespace App\Events;

use App\Models\InventoryMovement;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InventoryMovementCreatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public InventoryMovement $inventoryMovement;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(InventoryMovement $inventoryMovement)
    {
        $this->inventoryMovement = $inventoryMovement;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
