<?php

namespace Tests\Feature\Http\Controllers\Api\Modules\Printnode\ClientController;

use App\Modules\PrintNode\src\Models\Client;
use App\User;
use Tests\TestCase;

class StoreTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $admin = User::factory()->create()->assignRole('admin');
        $this->actingAs($admin, 'api');
    }

    /** @test */
    public function test_store_call_returns_ok()
    {
        $client = Client::factory()->make();

        $response = $this->post(route('api.settings.module.printnode.clients.store', $client));

        $response->assertStatus(400);
    }
}
