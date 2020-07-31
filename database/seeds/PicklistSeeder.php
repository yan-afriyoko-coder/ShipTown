<?php

use App\Models\Picklist;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class PicklistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Picklist::class, 2)
            ->create();

        collect(factory(Picklist::class, 2)->make())
            ->map(function ($picklistEntry) {

                $suffix = Arr::random(['-blue', '-red', '-green', '-xl', '-small-orange']);

                $picklistEntry['product_id'] = null;
                $picklistEntry['sku_ordered'] = $picklistEntry['sku_ordered'] . $suffix;
                $picklistEntry['name_ordered'] = $picklistEntry['name_ordered'] . $suffix;

                return $picklistEntry->save();
            });

        collect(factory(Picklist::class, 2)->make())
            ->map(function ($picklistEntry) {

                $suffix = Arr::random(['-blue', '-red', '-green', '-xl', '-small-orange']);

                $product = Product::query()->inRandomOrder()->first();
                $picklistEntry['product_id'] = $product->getKey();
                $picklistEntry['sku_ordered'] = $product->sku . $suffix;
                $picklistEntry['name_ordered'] = $picklistEntry['name_ordered'] . $suffix;

                return $picklistEntry->save();
            });



    }
}
