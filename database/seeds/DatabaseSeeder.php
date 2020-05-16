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

        $this->call([
            ProductsSeeder::class
        ]);

        // create demo user
        if(User::query()
            ->where('email','=','demo@products.management')
            ->doesntExist()) {
                factory(User::class, 1)
                    ->create(['email' => 'demo@products.management']);
        }

        \App\Models\Product::query()
            ->get()
            ->each(function (\App\Models\Product $product) {
                factory(\App\Models\Inventory::class)
                    ->create([
                        'product_id' => $product->id,
                        'location_id' => 1
                    ]);
            });

        \App\Models\Product::query()
            ->get()
            ->each(function (\App\Models\Product $product) {
                factory(\App\Models\Inventory::class)
                    ->create([
                        'product_id' => $product->id,
                        'location_id' => 1
                    ]);
            });

        \App\Models\Product::query()
            ->get()
            ->each(function (\App\Models\Product $product) {
                factory(\App\Models\Inventory::class)
                    ->create([
                        'product_id' => $product->id,
                        'location_id' => 99
                    ]);
            });

        \App\Models\Product::query()
            ->get()
            ->each(function (\App\Models\Product $product) {
                factory(\App\Models\Inventory::class)
                    ->create([
                        'product_id' => $product->id,
                        'location_id' => 100
                    ]);
            });

        \App\Models\Product::query()
            ->get()
            ->each(function (\App\Models\Product $product) {
                factory(\App\Models\Inventory::class)
                    ->create([
                        'product_id' => $product->id,
                        'location_id' => 999
                    ]);
            });

        $this->call([
            OrdersSeeder::class
        ]);

        // $this->call(UsersTableSeeder::class);
    }
}
