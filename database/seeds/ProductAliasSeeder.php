<?php

use App\Models\ProductAlias;
use Illuminate\Database\Seeder;

class ProductAliasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(!ProductAlias::query()->where(['alias' => '45'])->exists()) {
            factory(ProductAlias::class)->create(['alias'    => '45']);
        }

        factory(ProductAlias::class, 10)->create();
    }
}
