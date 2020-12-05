<?php

namespace App\Events\Product;

use App\Models\Product;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Spatie\Tags\Tag;

/**
 * Class TagAttachedEvent
 * @package App\Events\Product
 */
class TagAttachedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Product
     */
    private $product;

    /**
     * @var
     */
    private $tag;

    /**
     * Create a new event instance.
     *
     * @param Product $product
     * @param string $tag
     */
    public function __construct(Product $product, string $tag)
    {
        $this->product = $product;
        $this->tag = $tag;
    }

    /**
     * @return Product
     */
    public function product()
    {
        return $this->product;
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
