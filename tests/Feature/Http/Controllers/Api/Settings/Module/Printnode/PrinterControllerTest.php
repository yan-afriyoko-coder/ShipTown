<?php

namespace Tests\Feature\Http\Controllers\Api\Settings\Module\Printnode;

use App\User;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Api\Settings\Module\Printnode\PrinterController
 */
class PrinterControllerTest extends TestCase
{
    /**
     * @test
     */
    public function index_returns_an_ok_response()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user, 'api')->getJson(route('printers.index'));

        $response->assertStatus(422);
//        $response->assertJsonStructure([
////            'meta',
////            'links',
//            'data' => [
//                '*' => [
//                ]
//            ]
//        ]);
    }

    // test cases...
}
