<?php

namespace App\Events\Product;

use App\Models\ProductPrice;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class CreatedEvent
 * @package App\Events\Product
 */
class ProductPriceUpdatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var ProductPrice
     */
    public ProductPrice $product_price;

    /**
     * Create a new event instance.
     *
     * @param ProductPrice $product_price
     */
    public function __construct(ProductPrice $product_price)
    {
        $this->product_price = $product_price;
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
