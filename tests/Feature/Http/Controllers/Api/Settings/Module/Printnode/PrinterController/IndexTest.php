<?php

namespace Tests\Feature\Http\Controllers\Api\Settings\Module\Printnode\PrinterController;

use App\User;
use Tests\TestCase;

class IndexTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $admin = factory(User::class)->create()->assignRole('admin');
        $this->actingAs($admin, 'api');
    }

    /** @test */
    public function test_index_call_returns_ok()
    {
        $response = $this->get(route('api.settings.module.printnode.printers.index'));

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => [],
            ],
        ]);
    }
}
