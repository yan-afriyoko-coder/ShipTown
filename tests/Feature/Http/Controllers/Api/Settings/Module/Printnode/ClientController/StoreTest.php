<?php

namespace Tests\Feature\Http\Controllers\Api\Settings\Module\Printnode\ClientController;

use App\Modules\PrintNode\src\Models\Client;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $admin = factory(User::class)->create()->assignRole('admin');
        $this->actingAs($admin, 'api');
    }

    /** @test */
    public function test_store_call_returns_ok()
    {
        $client = factory(Client::class)->make();

        $response = $this->post(route('api.settings.module.printnode.clients.store', $client));

        $response->assertStatus(400);
    }
}
