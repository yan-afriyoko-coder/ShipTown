<?php

namespace Tests\Routes\Api;

use App\Models\Order;
use Spatie\Activitylog\Models\Activity;
use Tests\Routes\AuthenticatedRoutesTestCase;

class LogRoutesTest extends AuthenticatedRoutesTestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        Activity::query()->forceDelete();

        $order = factory(Order::class)->create();

        $order->update(['status_code' => 'something_random_52']);

        $response = $this->get('api/logs?subject_type=App\Models\Order');

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'subject_id',
                    'subject_type',
                    'causer_id',
                    'causer_type',
                    'properties' => [
                        '*' => []
                    ],
                    'changes' => []
                ]
            ],
            'meta',
        ]);
    }
}
