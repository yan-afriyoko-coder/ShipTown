<?php

namespace Tests\Feature\import\orders;

use App\Jobs\JobImportOrderApi2Cart;
use Illuminate\Support\Facades\Queue;
use Tests\Feature\AuthorizedUserTestCase;
use Tests\TestCase;

class api2CartTest extends TestCase
{
    use AuthorizedUserTestCase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_get_route()
    {
        Queue::fake();

        $response = $this->get('api/import/orders/api2cart');

        Queue::assertPushed(JobImportOrderApi2Cart::class);

        $response->assertStatus(200);
    }
}
