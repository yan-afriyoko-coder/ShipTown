<?php

namespace Tests\Feature\Http\Controllers\Api\Settings\Module\DpdIreland\DpdIrelandController;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_config_update()
    {
        $user = factory(User::class)->create();
        $response = $this->actingAs($user, 'api')->post(route('api.settings.module.dpd-ireland.store'), [
            'live' => false,
            'user' => 'someuser',
            'password' => 'somepassword',
            'token' => 'sometoken',
            'contact' => 'DPD Contact',
            'contact_telephone' => '0860000000',
            'contact_email' => 'testemail@dpd.ie',
            'business_name' => 'DPD API Test Limited',
            'address_line_1' => 'Athlone Business Park',
            'address_line_2' => 'Dublin Road',
            'address_line_3' => 'Athlone',
            'address_line_4' => 'Co. Westmeath',
            'country_code' => 'IE',
        ]);

        $response->assertSuccessful();
    }
}
