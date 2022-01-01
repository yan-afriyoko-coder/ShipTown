<?php

namespace Tests\Feature\Routes\Web\Admin;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 *
 */
class SettingsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var string
     */
    protected string $uri = '/admin/settings';

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
        $this->actingAs($this->user, 'web');
    }

    /** @test */
    public function test_admin_call()
    {
        $this->user->assignRole('admin');

        $response = $this->get($this->uri);

        $response->assertSuccessful();
    }

    /** @test */
    public function test_user_call()
    {
        $response = $this->get($this->uri);

        $response->assertForbidden();
    }
}
