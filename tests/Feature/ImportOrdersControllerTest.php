<?php

namespace Tests\Feature;

use App\Jobs\JobImportOrderApi2Cart;
use App\User;
use Illuminate\Support\Facades\Queue;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ImportOrdersControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_if_job_is_pushed_to_queue()
    {
        Queue::fake();

        Passport::actingAs(
            factory(User::class)->create()
        );

        $response = $this->get('/api/import/orders/api2cart');

        $response->assertStatus(200);

        Queue::assertPushed(JobImportOrderApi2Cart::class);
    }
}
