<?php

namespace Tests\Feature;

use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TopicSubscriptionsControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_topic_subscription () {

        $data = "https://phpunit.topic.subscriptions.test";

        Passport::actingAs(
            factory(User::class)->create()
        );

        $this->json('POST', 'api/topics/products/subscriptions', [$data])
            ->assertStatus(200);

    }
}
