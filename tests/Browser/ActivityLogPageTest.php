<?php

namespace Tests\Browser;

use App\Models\Product;
use App\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Throwable;

class ActivityLogPageTest extends DuskTestCase
{
    /**
     * A basic browser test example.
     *
     * @throws Throwable
     *
     * @return void
     */
    public function testBasicExample()
    {
        $this->browse(function (Browser $browser) {
            Product::factory()->create();

            $user = User::factory()->create();
            $user->assignRole('admin');

            $browser->loginAs($user)
                ->visit('/admin/activity-log')
                ->assertAuthenticated()
                ->screenshot('ActivityLog');
        });
    }
}
