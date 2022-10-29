<?php

namespace App\Events\Product;

use App\Models\Product;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class ProductTagAttachedEvent
 * @package App\Events\Product
 */
class ProductTagAttachedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Product
     */
    public Product $product;

    /**
     * @var string
     */
    public string $tag;

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
    public function product(): Product
    {
        return $this->product;
    }

    /**
     * @return string
     */
    public function tag(): string
    {
        return $this->tag;
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
