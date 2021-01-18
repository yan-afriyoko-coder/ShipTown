<?php

namespace Tests\Feature\Models;

use App\Events\Product\TagAttachedEvent;
use App\Models\Product;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\TestCase;

class ProductModelTest extends TestCase
{
    public function test_if_dispatched_tag_attached_event()
    {
        $product = factory(Product::class)->create();

        Event::fake();

        $product->attachTag('Test');

        Event::assertDispatched(TagAttachedEvent::class);
    }
}
