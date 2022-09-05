<?php

namespace Tests\Feature\Routes\Web\Data_collector;

use App\Models\DataCollection;
use App\Models\Warehouse;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 *
 */
class Data_collection_idTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var string
     */
    protected string $uri = 'data-collector';

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

        $dataCollection = factory(DataCollection::class)->create([
            'warehouse_id' => factory(Warehouse::class)->create()->getKey(),
            'name' => 'test'
        ]);

        $response = $this->get($this->uri . '/' . $dataCollection->id);

        $response->assertSuccessful();
    }

    /** @test */
    public function test_admin_call()
    {
        $this->user->assignRole('admin');

        $this->actingAs($this->user, 'web');

        $dataCollection = factory(DataCollection::class)->create([
            'warehouse_id' => factory(Warehouse::class)->create()->getKey(),
            'name' => 'test'
        ]);

        $response = $this->get($this->uri . '/' . $dataCollection->id);

        $response->assertSuccessful();
    }
}
