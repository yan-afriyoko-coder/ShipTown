<?php

namespace Tests\Feature\Routes\Web\Reports;

use App\Models\Product;
use App\Models\Warehouse;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 *
 */
class RestockingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var string
     */
    protected string $uri = '/reports/restocking';

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

        $response->assertRedirect('/login');
    }

    /** @test */
    public function test_user_call()
    {
        $this->actingAs($this->user, 'web');

        factory(Warehouse::class)->create();
        factory(Product::class)->create();

        $response = $this->get($this->uri);

        $response->assertRedirect();
    }

    /** @test */
    public function test_admin_call()
    {
        $this->actingAs($this->user, 'web');

        $this->user->assignRole('admin');

        factory(Warehouse::class)->create();
        factory(Product::class)->create();

        $response = $this->get($this->uri);

        $response->assertRedirect();
    }
}
