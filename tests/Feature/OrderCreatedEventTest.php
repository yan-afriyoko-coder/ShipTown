<?php

namespace Tests\Feature;

use App\Events\OrderCreatedEvent;
use App\Models\Order;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderCreatedEventTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
//        Event::fake();

        OrderCreatedEvent::dispatch(new Order());

//        Event::assertDispatched(OrderCreatedEvent::class);

    }
}
