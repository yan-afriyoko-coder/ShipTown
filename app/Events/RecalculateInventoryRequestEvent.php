<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class RecalculateInventoryRequestEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Collection $inventoryRecordsIds;

    public function __construct($inventoryRecordsIds)
    {
        $this->inventoryRecordsIds = $inventoryRecordsIds;
    }

    public function broadcastOn(): Channel|PrivateChannel
    {
        return new PrivateChannel('channel-name');
    }
}
