<?php

namespace Tests\Feature\Routes\Web;

use App\User;
use Tests\TestCase;

/**
 *
 */
class RegisterTest extends TestCase
{
    /**
     * @var string
     */
    protected string $uri = '/register';

    /**
     * @var User
     */
    protected User $user;

    /**
     *
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
    }

    /** @test */
    public function test_if_uri_set()
    {
        $this->assertNotEmpty($this->uri);
    }

    /** @test */
    public function test_guest_call()
    {
        $response = $this->get($this->uri);

        if (User::query()->doesntExist()) {
            $response->assertOk();
        } else {
            $response->assertNotFound();
        }
    }

    /** @test */
    public function test_user_call()
    {
        $this->actingAs($this->user, 'web')
            ->get($this->uri)
            ->assertNotFound();
    }

    /** @test */
    public function test_admin_call()
    {
        $this->user->assignRole('admin');

        $this->actingAs($this->user, 'web')
            ->get($this->uri)
            ->assertNotFound();
    }
}
