<?php

use App\Models\Picklist;
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
        factory(Picklist::class, 20)
            ->create();

        collect(factory(Picklist::class, 10)->make())
            ->map(function ($picklistEntry) {

                $suffix = Arr::random(['-blue', '-red', '-green', '-xl', '-small-orange']);

                $picklistEntry['product_id'] = null;
                $picklistEntry['sku_ordered'] = $picklistEntry['sku_ordered'] . $suffix;
                $picklistEntry['name_ordered'] = $picklistEntry['name_ordered'] . $suffix;

                return $picklistEntry->save();
            });

    }
}
