<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Pick;
use App\Models\PickRequest;
use App\Services\PicklistService;
use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PicksTest extends TestCase
{
    /**
     *
     */
    public function testIfCreatesPicsRequestsWhenStatusChange()
    {
        PickRequest::query()->forceDelete();

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
