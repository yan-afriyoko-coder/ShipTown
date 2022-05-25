<?php

namespace Tests;

use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\ProductAlias;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use JMac\Testing\Traits\AdditionalAssertions;
use Spatie\Tags\Tag;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use AdditionalAssertions;

    protected function setUp(): void
    {
        ray()->clearAll();
        ray()->className($this)->blue();

        parent::setUp();

        Product::query()->forceDelete();
        Inventory::query()->forceDelete();
        ProductAlias::query()->forceDelete();
        OrderProduct::query()->forceDelete();
        Order::query()->forceDelete();
//        Tag::query()->forceDelete();
    }
}
