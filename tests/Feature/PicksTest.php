<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Pick;
use App\Models\PickRequest;
use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;

class PicksTest extends TestCase
{
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
