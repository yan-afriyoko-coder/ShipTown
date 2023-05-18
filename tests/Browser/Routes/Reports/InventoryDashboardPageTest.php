<?php

namespace Tests\Browser\Routes\Reports;

use App\Models\Warehouse;
use App\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Throwable;

class InventoryDashboardPageTest extends DuskTestCase
{
    private string $uri = '/reports/inventory-dashboard';

    /**
     * @throws Throwable
     */
    public function testInventoryDashboardPage()
    {
        /** @var Warehouse $warehouse */
        $warehouse = Warehouse::factory()->create();

        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole('admin');
        $user->update(['warehouse_id' => $warehouse->id]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->disableFitOnFailure();
            $browser->loginAs($user);
            $browser->visit($this->uri);
            $browser->assertPathIs($this->uri);
            $browser->assertSourceMissing('Server Error');

            $this->markTestIncomplete('This test has not been implemented yet.');
        });
    }

    /**
     * @throws Throwable
     */
    public function testBasics()
    {
        $this->basicUserAccessTest($this->uri, true);
        $this->basicAdminAccessTest($this->uri, true);
        $this->basicGuestAccessTest($this->uri);
    }
}
