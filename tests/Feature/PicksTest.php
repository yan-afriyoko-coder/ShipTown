<?php

namespace Tests\Feature;

use App\Events\PickPickedEvent;
use App\Events\PickQuantityRequiredChangedEvent;
use App\Events\PickUnpickedEvent;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Pick;
use App\Models\PickRequest;
use App\Models\Product;
use App\User;
use Illuminate\Support\Facades\Event;
use Laravel\Passport\Passport;
use Tests\TestCase;

class PicksTest extends TestCase
{
    public function testIfOrderPickedAtIsUpdated()
    {
        Order::query()->forceDelete();
        OrderProduct::query()->forceDelete();
        PickRequest::query()->forceDelete();
        Pick::query()->forceDelete();

        $user = factory(User::class)->create();

        factory(Product::class)->create();

        $order = factory(Order::class)
            ->with('orderProducts', 2)
            ->create();

        $order->update(['status_code' => 'picking']);

        $picks = Pick::query()->whereNull('picked_at')->get();

        foreach ($picks as $pick) {
            $pick->pick($user, $pick->quantity_required);
        }

        $this->assertTrue(
            Order::query()->whereNotNull('picked_at')->exists()
        );
    }

    public function testIfOrderProductQuantityPickedIsPopulated()
    {
        Order::query()->forceDelete();
        OrderProduct::query()->forceDelete();
        PickRequest::query()->forceDelete();
        Pick::query()->forceDelete();

        $user = factory(User::class)->create();

        factory(Product::class)->create();

        $order = factory(Order::class)
            ->with('orderProducts')
            ->create();

        $order->update(['status_code' => 'picking']);

        $pick = Pick::query()->whereNull('picked_at')->first();

        $pick->pick($user, $pick->quantity_required);

        $this->assertFalse(
            OrderProduct::query()->whereRaw('quantity_ordered <> quantity_picked')->exists()
        );
    }

    public function testIfPickRequestsAreRedistributedWhenQuantityRequiredChanged()
    {
        Passport::actingAs(
            factory(User::class)->create()
        );

        OrderProduct::query()->forceDelete();
        Order::query()->forceDelete();
        PickRequest::query()->forceDelete();
        Pick::query()->forceDelete();

        factory(Product::class, [])->create();

        $orders = factory(Order::class, rand(2, 5))
            ->with('orderProducts', rand(2, 5))
            ->create(['status_code' => 'processing']);

        foreach ($orders as $order) {
            $order->update(['status_code' => 'picking']);
        }

        $pick = Pick::query()->first();

        $response = $this->putJson("/api/picks/".$pick['id'], [
            'quantity_picked' => $pick['quantity_required'] - rand(1, $pick['quantity_required']-1)
        ]);

        $response->assertStatus(200);

        $pick = $pick->refresh();

        $this->assertEquals(
            PickRequest::query()->sum('quantity_required'),
            Pick::query()->sum('quantity_required')
        );
    }

    public function testPickRequestSplit()
    {
        OrderProduct::query()->forceDelete();
        Order::query()->forceDelete();
        PickRequest::query()->forceDelete();
        Pick::query()->forceDelete();

        $order = factory(Order::class)
            ->with('orderProducts')
            ->create(['status_code' => 'processing'])
            ->update(['status_code' => 'picking']);

        $pickRequest = PickRequest::query()->first();

        $original_quantity = $pickRequest->quantity_required;


        $pickRequest->extractToNewPick(1);

        $this->assertEquals(
            $original_quantity,
            PickRequest::query()->sum('quantity_required')
        );
    }

    public function testIfPickQuantityChangedEventDispatched()
    {
        Event::fake([PickQuantityRequiredChangedEvent::class]);

        $user = factory(User::class)->create();
        $pick = factory(Pick::class)->create();

        $pick->pick($user, $pick->quantity_required - 1);

        Event::assertDispatched(PickQuantityRequiredChangedEvent::class);
    }

    public function testIfQuantityPickedSumsUpWhenUnpicked()
    {
        Passport::actingAs(
            factory(User::class)->create()
        );

        OrderProduct::query()->forceDelete();
        Order::query()->forceDelete();
        PickRequest::query()->forceDelete();
        Pick::query()->forceDelete();

        $order = factory(Order::class)
            ->with('orderProducts')
            ->create(['status_code' => 'processing'])
            ->update(['status_code' => 'picking']);

        $pick = Pick::query()->first();

        $response = $this->putJson("/api/picks/".$pick['id'], [
            'quantity_picked' => $pick['quantity_required']
        ]);

        $response = $this->putJson("/api/picks/".$pick['id'], [
            'quantity_picked' => 0
        ]);

        $response->assertStatus(200);

        $pick = $pick->refresh();
        $this->assertNull($pick->picker_user_id, 'User not set null');
        $this->assertNull($pick->picket_at, 'Picked_at is not set null');

        $this->assertEquals(
            0,
            PickRequest::query()->sum('quantity_picked')
        );
    }

    public function testIdDispatchedUnpickPickedEvent()
    {
        Event::fake([PickUnpickedEvent::class]);
        $user = factory(User::class)->create();
        $pick = factory(Pick::class)->create();

        $pick->pick($user, $pick->quantity_required);
        $pick->pick($user, 0);

        Event::assertDispatched(PickUnpickedEvent::class);
    }

    public function testIdDispatchedPickPickedEvent()
    {
        Event::fake([PickPickedEvent::class]);
        $user = factory(User::class)->create();
        $pick = factory(Pick::class)->create();

        $pick->pick($user, $pick->quantity_required);

        Event::assertDispatched(PickPickedEvent::class);
    }

    public function testIfQuantityPickedSumsUp()
    {
        Passport::actingAs(
            factory(User::class)->create()
        );

        OrderProduct::query()->forceDelete();
        Order::query()->forceDelete();
        PickRequest::query()->forceDelete();
        Pick::query()->forceDelete();

        $order = factory(Order::class)
            ->with('orderProducts')
            ->create(['status_code' => 'processing'])
            ->update(['status_code' => 'picking']);


        $pick = Pick::query()->first();

        $response = $this->putJson("/api/picks/".$pick['id'], [
            'quantity_picked' => $pick['quantity_required']
        ]);

        $response->assertStatus(200);

        $this->assertEquals(
            PickRequest::query()->sum('quantity_picked'),
            Pick::query()->sum('quantity_required')
        );
    }

    public function testPickModelPickMethod()
    {
        Passport::actingAs(
            factory(User::class)->create()
        );

        PickRequest::query()->forceDelete();
        Pick::query()->forceDelete();

        $order = factory(Order::class)
            ->with('orderProducts')
            ->create(['status_code' => 'processing'])
            ->update(['status_code' => 'picking']);

        $response = $this->get('/api/picks');

        $this->assertEquals(1, $response->json('total'));

        $pick = $response->json()['data'][0];

        $response = $this->putJson("/api/picks/".$pick['id'], [
            'quantity_picked' => $pick['quantity_required']
        ]);

        $response->assertStatus(200);
    }

    public function testIfSumsUpCorrectly()
    {
        OrderProduct::query()->forceDelete();
        Order::query()->forceDelete();
        PickRequest::query()->forceDelete();
        Pick::query()->forceDelete();

        $orders = factory(Order::class, rand(1, 5))
            ->with('orderProducts', rand(1, 5))
            ->create(['status_code' => 'processing']);

        foreach ($orders as $order) {
            $order->update(['status_code' => 'picking']);
        }

        $this->assertEquals(
            OrderProduct::query()->sum('quantity_ordered'),
            PickRequest::query()->sum('quantity_required')
        );

        $this->assertEquals(
            PickRequest::query()->sum('quantity_required'),
            Pick::query()->sum('quantity_required')
        );
    }

    public function testIfCreatesPicsWhenStatusChange()
    {
        PickRequest::query()->forceDelete();
        Pick::query()->forceDelete();

        $order = factory(Order::class)
            ->with('orderProducts')
            ->create(['status_code' => 'processing']);

        $order->update(['status_code' => 'picking']);

        $this->assertTrue(
            Pick::query()->exists(),
            'No picks added to picklist'
        );
    }

    /**
     *
     */
    public function testIfCreatesPicsRequestsWhenStatusChange()
    {
        PickRequest::query()->forceDelete();
        Pick::query()->forceDelete();

        $order = factory(Order::class)
            ->with('orderProducts')
            ->create(['status_code' => 'processing']);

        $order->update(['status_code' => 'picking']);

        $this->assertTrue(
            PickRequest::query()->exists(),
            'No pick requests exists'
        );
    }

    /**
     *
     */
    public function testGetAuthenticated()
    {
        Passport::actingAs(
            factory(User::class)->create()
        );

        $response = $this->get('/api/picks');

        $response->assertStatus(200);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testGetUnauthenticated()
    {
        $response = $this->get('/api/picks');

        $response->assertStatus(302);
    }
}
