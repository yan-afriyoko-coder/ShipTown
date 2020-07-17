<?php

namespace Tests\Unit\Observers;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Picklist;
use Sentry\Event;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AddToPicklistWhenOrderCreatedTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_if_all_products_are_added_to_picklist()
    {


    }
}
