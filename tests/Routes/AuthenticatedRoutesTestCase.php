<?php

namespace Tests\Routes;

use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;

class AuthenticatedRoutesTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        User::query()->forceDelete();

        Passport::actingAs(
            factory(User::class)->create()
        );
    }
}
