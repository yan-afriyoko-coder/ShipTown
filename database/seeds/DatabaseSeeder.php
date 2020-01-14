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
            ->where('email','=','demo@demo.com')
            ->doesntExist()) {
                factory(User::class, 1)
                    ->create(['email' => 'demo@demo.com']);
        }


        // for each user create sample product list
        User::all()->each(function (User $u) {

           $u->products()->saveMany(
               factory(\App\Models\Product::class, 500)
                   ->make()
           );

        });


        // for each user create some products where quantity_available should be 0
        User::all()->each(function (User $u) {

            $u->products()->saveMany(
               factory(\App\Models\Product::class, 50)
                   ->make([
                       "quantity" => 50,
                       "quantity_reserved" => 50
                   ])
           );

        });


        // create inventory entries for random products
        factory(\App\Models\Inventory::class, 1500)
            ->create([
                'location_id' => 99
            ]);

        // create inventory entries for random products
        factory(\App\Models\Inventory::class, 1500)
            ->create([
                'location_id' => 100
            ]);

        // create inventory entries for random products
        factory(\App\Models\Inventory::class, 1500)
            ->create();


        // $this->call(UsersTableSeeder::class);
    }
}
