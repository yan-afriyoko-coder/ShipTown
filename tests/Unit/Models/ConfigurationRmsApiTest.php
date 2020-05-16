<?php

namespace Tests\Unit;


use Tests\TestCase;

use App\Models\ConfigurationRmsApi;

class ConfigurationRmsApiTest extends TestCase
{
    public function test_encrypts_password()
    {
        $config = new ConfigurationRmsApi();
        $config->password = 'foo';

        // Tests that that password is encrypted before saving to the database.
        $this->assertNotEquals('foo', $config->password);
        // Make sure we can reuse the password when needed.
        $this->assertEquals('foo', \Crypt::decryptString($config->password));
    }
}
