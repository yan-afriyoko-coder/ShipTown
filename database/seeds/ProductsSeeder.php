<?php

use App\Models\Product;
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
        if(!Product::query()->where('sku','=','123456')->exists()) {
            factory(Product::class)->create(['sku' => '123456']);
        }

        factory(Product::class, 50)->create();

        factory(Product::class, 10)->create([
            "quantity" => 50,
            "quantity_reserved" => 50
        ]);


    }
}
