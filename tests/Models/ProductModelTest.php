<?php

namespace Tests\Feature\Models;

use App\Events\Product\TagAttachedEvent;
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

        Event::assertDispatched(TagAttachedEvent::class);
    }
}
