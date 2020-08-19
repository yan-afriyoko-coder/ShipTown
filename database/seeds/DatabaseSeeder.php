<?php

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
        // create demo user
        if(User::query()
            ->where('email','=','demo@products.management')
            ->doesntExist()) {
                $user = factory(User::class, 1)
                    ->create(['email' => 'demo@products.management']);
                $user->first()->assignRole('admin');
        }

        $this->call([
            ProductsSeeder::class,
            InventorySeeder::class,
            OrdersSeeder::class,
            Api2CartOrderImportSeeder::class,
            RmsapiConnectionSeeder::class,
            RmsapiProductImportSeeder::class,
            PicklistSeeder::class,
            PacklistSeeder::class,
            ProductAliasSeeder::class,
            SingleLineOrderPicklistSeeder::class,
        ]);

    }
}
