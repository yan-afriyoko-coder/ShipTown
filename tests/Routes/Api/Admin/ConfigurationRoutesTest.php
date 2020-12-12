<?php

namespace Tests\Routes\Api\Admin;

use App\Models\Configuration;
use Tests\Routes\AuthenticatedRoutesTestCase;

class ConfigurationRoutesTest extends AuthenticatedRoutesTestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testStore()
    {
        auth()->user()->assignRole('admin');

        $configuration = [
            'key' => 'testKey',
            'value' => 'testValue',
        ];

        $response = $this->post('/api/configuration', $configuration);

        $response->assertStatus(200);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testShow()
    {
        Configuration::query()->forceDelete();

        auth()->user()->assignRole('admin');

        Configuration::firstOrCreate([
            'key' => 'testKey',
            'value' => 'testValue',
        ]);

        $response = $this->get('/api/configuration/testKey');

        $response->assertStatus(200);
    }
}
