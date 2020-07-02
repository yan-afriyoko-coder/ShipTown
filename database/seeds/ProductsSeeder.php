<?php

use Illuminate\Database\Seeder;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Models\Product::class)->create(['sku' => '123456']);

        factory(\App\Models\Product::class, 50)->create();

        factory(\App\Models\Product::class, 10)->create([
            "quantity" => 50,
            "quantity_reserved" => 50
        ]);


    }
}
