<?php

namespace Tests\Unit\Jobs\Picklists;

use App\Jobs\Picklists\RebuildPicklistsJob;
use App\Models\Order;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RebuildPicklistsJobTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_if_all_order_products_are_added_to_picklists()
    {
        RebuildPicklistsJob::dispatchNow();

        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
