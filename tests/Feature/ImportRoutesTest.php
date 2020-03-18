<?php

namespace Tests\Feature;

use App\Jobs\ImportOrdersFromApi2cartJob;
use App\User;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Queue;
use Laravel\Passport\Passport;
use Mockery\Generator\StringManipulation\Pass\Pass;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ImportRoutesTest extends TestCase
{
    public function test_if_import_from_api2cart_route_works()
    {
        Bus::fake();

        Passport::actingAs(
            factory(User::class)->create()
        );

        $response = $this->get('api/import/orders/from/api2cart');

        $response->assertOk();

        Bus::assertDispatched(ImportOrdersFromApi2cartJob::class);
    }
}
