<?php

namespace Tests\Feature\Routes\Web;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 *
 */
class RegisterTest extends TestCase
{
    use RefreshDatabase;

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

        $response->assertOk();
    }

    /** @test */
    public function test_user_call()
    {
        $this->user = factory(User::class)->create();
        $this->actingAs($this->user, 'web');

        $response = $this->get($this->uri);

        $response->assertRedirect('/dashboard');
    }

    /** @test */
    public function test_admin_call()
    {
        $this->user = factory(User::class)->create();
        $this->user->assignRole('admin');

        $this->actingAs($this->user, 'web');

        $response = $this->get($this->uri);

        $response->assertRedirect('/dashboard');
    }
}
