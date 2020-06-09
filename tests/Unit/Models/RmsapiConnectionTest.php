<?php

namespace Tests\Unit\Models;

use Tests\TestCase;

use App\Models\RmsapiConnection;

class RmsapiConnectionTest extends TestCase
{
    public function test_encrypts_password()
    {
        $config = new RmsapiConnection();
        $config->password = 'foo';

        // Tests that that password is encrypted before saving to the database.
        $this->assertNotEquals('foo', $config->password);
        // Make sure we can reuse the password when needed.
        $this->assertEquals('foo', \Crypt::decryptString($config->password));
    }
}
