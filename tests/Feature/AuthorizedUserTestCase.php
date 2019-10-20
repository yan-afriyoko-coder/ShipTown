<?php


namespace Tests\Feature;


use App\User;
use Laravel\Passport\Passport;

trait AuthorizedUserTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Passport::actingAs(
            factory(User::class)->create()
        );
    }

}
