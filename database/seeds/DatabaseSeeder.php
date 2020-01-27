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


        // for each user create sample product sku = 12345
        User::all()->each(function (User $u) {

            $product = factory(\App\Models\Product::class)
                ->make(['sku' => '12345']);

            $u->products()->save($product);

        });


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


        \App\Models\Product::withoutGlobalScope(AuthenticatedUserScope::class)
            ->get()
            ->each(function (\App\Models\Product $product) {
                factory(\App\Models\Inventory::class)
                    ->create([
                        'product_id' => $product->id,
                        'location_id' => 1
                    ]);
            });

        \App\Models\Product::withoutGlobalScope(AuthenticatedUserScope::class)
            ->get()
            ->each(function (\App\Models\Product $product) {
                factory(\App\Models\Inventory::class)
                    ->create([
                        'product_id' => $product->id,
                        'location_id' => 99
                    ]);
            });

        \App\Models\Product::withoutGlobalScope(AuthenticatedUserScope::class)
            ->get()
            ->each(function (\App\Models\Product $product) {
                factory(\App\Models\Inventory::class)
                    ->create([
                        'product_id' => $product->id,
                        'location_id' => 100
                    ]);
            });

        // $this->call(UsersTableSeeder::class);
    }
}
