<?php

namespace Tests\Feature\Http\Controllers\Api\Settings\Module\Printnode\PrintJobController;

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
        $this->markTestIncomplete();
//        $response = $this->post(route('api.settings.module.printnode.printjobs.store'), [
//            'printer_id' => '1'
//        ]);
//
//        $response->assertStatus(200);
    }
}
