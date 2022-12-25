<?php

namespace Tests\Feature\Http\Controllers\Api\Settings\WidgetController;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $admin = User::factory()->create()->assignRole('admin');
        $this->actingAs($admin, 'api');
    }

    /** @test */
    public function test_store_call_returns_ok()
    {
        $response = $this->post(route('widgets.store'), [
            'name'   => 'Tes widget',
            'config' => []
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'name',
                'config' => [],
                'id'
            ],
        ]);
    }
}
