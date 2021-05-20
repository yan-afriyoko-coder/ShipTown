<?php

namespace Tests\Models;

use App\Events\Product\ProductTagAttachedEvent;
use App\Models\Product;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\TestCase;
use Spatie\Tags\Tag;

class ProductModelTest extends TestCase
{
    public function test_if_dispatched_tag_attached_event()
    {
        Product::query()->forceDelete();
        Tag::query()->forceDelete();

        $product = factory(Product::class)->create();

        Event::fake();

        $product->attachTag('Test');

        Event::assertDispatched(ProductTagAttachedEvent::class);
    }
}
