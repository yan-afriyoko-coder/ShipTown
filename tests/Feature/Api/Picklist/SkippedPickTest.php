<?php

namespace Tests\Feature\Api\Picklist;

use App\Models\Picklist;
use App\Notifications\SkippedPickNotification;
use App\User;
use Illuminate\Support\Facades\Notification;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Thomasjohnkane\Snooze\Models\ScheduledNotification;

class SkippedPickTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        ScheduledNotification::query()->delete();

        $user = factory(User::class)->create();

        Passport::actingAs($user);

        $pick = factory(Picklist::class)->create();

        $response = $this->postJson('api/picklist/'.$pick->id, [
            "quantity_picked" => 0,
            "undo" => false,
        ]);

        $response->assertStatus(200);

        // its hard to test delayed notification using Notification:assertSentTo
        // so we just check if the notification is in database
        $this->assertTrue(
            ScheduledNotification::query()->exists()
        );
    }
}
