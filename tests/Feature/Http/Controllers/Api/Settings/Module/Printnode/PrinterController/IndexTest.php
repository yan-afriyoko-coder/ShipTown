<?php

namespace Tests\Feature\Http\Controllers\Api\Settings\Module\Printnode\PrinterController;

use App\User;
use Tests\TestCase;

class IndexTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $user = factory(User::class)->create()->assignRole('user');
        $this->actingAs($user, 'api');
    }

    /** @test */
    public function test_index_call_returns_ok()
    {
        $response = $this->get('api/modules/printnode/printers');

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => [],
            ],
        ]);
    }
}
