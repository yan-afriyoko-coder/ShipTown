<?php

use App\Scopes\AuthenticatedUserScope;
use App\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \Illuminate\Support\Facades\Event::fake();

        // create demo user
        if(User::query()
            ->where('email','=','demo@products.management')
            ->doesntExist()) {
                factory(User::class, 1)
                    ->create(['email' => 'demo@products.management']);
        }

        $this->call([
            ProductsSeeder::class,
            InventorySeeder::class,
            OrdersSeeder::class
        ]);

    }
}
